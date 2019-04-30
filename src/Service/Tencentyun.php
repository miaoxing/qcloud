<?php

namespace Miaoxing\Tencentyun\Service;

use Tencentyun\Auth;
use Tencentyun\ImageV2;

/**
 * 腾讯云万象图片服务
 *
 * 注意,命名与官方保持一致
 *
 * @property TencentyunCos $tencentyunCos
 * @link https://github.com/tencentyun/php-sdk
 */
class Tencentyun extends \Miaoxing\File\Service\File
{
    /**
     * 应用ID
     *
     * 重要: appId,secretId,secretKey此处修改无效,需到Tencentyun\Conf中更改
     *
     * @var int
     * @see Tencentyun\Conf::APPID
     */
    protected $appId;

    /**
     * @var string
     * @see Tencentyun\Conf::SECRET_ID
     */
    protected $secretId;

    /**
     * @var string
     * @see Tencentyun\Conf::SECRET_KEY
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * 替换腾讯云的默认域名
     *
     * @var string
     */
    protected $customDomain;

    /**
     * 是否对返回图片进行签名
     *
     * @var bool
     */
    protected $sign = false;

    /**
     * 根据文件类型调用不同的上传服务
     *
     * {@inheritdoc}
     */
    public function write($file, $ext = '', $customName = '')
    {
        if ($this->isImageExt($this->getExt($file, $ext))) {
            return $this->processWrite($file, $ext, $customName);
        } elseif ($this->isVoiceExt($this->getExt($file, $ext))) {
            return $this->tencentyunCos->processWriteForVoice($file, $ext, $customName);
        } else {
            return $this->tencentyunCos->write($file, $ext, $customName);
        }
    }

    /**
     * 需转换的语音类型特殊处理
     * @param $file
     * @param string $ext
     * @param string $customName
     * @return array
     */
    public function processWriteForVoice($file, $ext = '', $customName = '')
    {
        // 1. 如果是远程,下载到本地
        $localFile = $this->downloadIfRemote($file, $ext);
        if (!$localFile) {
            return ['code' => -1, 'message' => sprintf('文件%s下载失败', $file)];
        }

        // 2. 文件转换
        $mp3 = $this->transform($file, $ext, $localFile);

        // 3. 上传到腾讯云
        $ret = $this->callUploadApi($mp3, $customName);
        if ($ret['code'] !== 0) {
            return $ret;
        }

        // 4. 计算文件信息
        $size = filesize($localFile);
        $md5 = md5_file($localFile);

        // 5. 上传完成后,移除本地文件
        $this->removeIfRemote($localFile, $file);
        $this->removeIfRemote($mp3, $file);

        // 6. 替换域名
        if ($this->customDomain) {
            $ret['url'] = $this->customDomain . parse_url($ret['url'], PHP_URL_PATH);
        }

        // 7. 增加签名
        if ($this->sign) {
            $ret['url'] = $this->signUrl($ret['url'], $this->sign);
        }

        return [
            'code' => 1,
            'message' => '上传成功',
            'url' => $ret['url'],
            'originalName' => $this->getFileName($file),
            'size' => $size,
            'md5' => $md5,
        ];
    }

    /**
     * 处理文件上传流程
     *
     * @param string $file
     * @param string $ext
     * @param string $customName
     * @return array
     */
    protected function processWrite($file, $ext = '', $customName = '')
    {
        // 1. 如果是远程,下载到本地
        $localFile = $this->downloadIfRemote($file, $ext);
        if (!$localFile) {
            return ['code' => -1, 'message' => sprintf('文件%s下载失败', $file)];
        }

        // 2. 上传到腾讯云
        $ret = $this->callUploadApi($localFile, $customName);
        if ($ret['code'] !== 0) {
            return $ret;
        }

        // 3. 计算文件信息
        $size = filesize($localFile);
        $md5 = md5_file($localFile);

        // 4. 上传完成后,移除本地文件
        $this->removeIfRemote($localFile, $file);

        // 5. 替换域名
        if ($this->customDomain) {
            $ret['url'] = $this->customDomain . parse_url($ret['url'], PHP_URL_PATH);
        }

        // 6. 对图片增加签名
        if ($this->sign) {
            $ret['url'] = $this->signUrl($ret['url']);
        }

        return [
            'code' => 1,
            'message' => '上传成功',
            'url' => $ret['url'],
            'originalName' => $this->getFileName($file),
            'width' => $ret['width'],
            'height' => $ret['height'],
            'size' => $size,
            'md5' => $md5,
        ];
    }

    /**
     * 调用接口上传文件
     *
     * @param string $file
     * @param string $customName 自定义的文件名称
     * @return array
     */
    protected function callUploadApi($file, $customName)
    {
        !$customName && $customName = $this->getFileUrl($file);
        $ret = ImageV2::upload($file, $this->bucket, $customName);
        if ($ret['code'] === 0) {
            $ret += [
                'url' => $ret['data']['downloadUrl'],
                'width' => $ret['data']['info'][0][0]['width'],
                'height' => $ret['data']['info'][0][0]['height'],
            ];
        }

        return $ret;
    }

    /**
     * 为地址生成访问签名,适用于开启Token防盗链的场景
     *
     * @param string $url
     * @param int $seconds 有效秒数
     * @return string
     * @link https://www.qcloud.com/doc/product/275/3805#4-.E7.AD.BE.E5.90.8D.E9.80.82.E7.94.A8.E5.9C.BA.E6.99.AF
     */
    public function signUrl($url, $seconds = 60)
    {
        // 格式为 http://xxx.xxx/fieldId
        $fileId = explode('/', $url, 4)[3];

        $expired = time() + $seconds;

        $sign = Auth::getAppSignV2($this->bucket, $fileId, $expired);

        return $url . '?sign=' . $sign;
    }

    /**
     * 移除URL中的签名
     *
     * @param string $url
     * @return string
     */
    public function unsignUrl($url)
    {
        return explode('?sign=', $url, 2)[0];
    }

    /**
     * 检查URL中是否存在签名
     *
     * @param string $url
     * @return bool
     */
    public function isSign($url)
    {
        return strpos($url, '?sign=') !== false;
    }
}
