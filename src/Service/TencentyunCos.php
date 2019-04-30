<?php

namespace Miaoxing\Tencentyun\Service;

use Qcloud_cos\Cosapi;

/**
 * 腾讯云对象存储服务
 *
 * @link http://www.qcloud.com/doc/product/227
 * @link https://github.com/tencentyun/cos-php-sdk
 */
class TencentyunCos extends Tencentyun
{
    /**
     * 应用ID
     *
     * 重要: appId,secretId,secretKey此处修改无效,需到Tencentyun\Conf中更改
     *
     * @var int
     * @see \Qcloud_cos\Conf::APPID
     */
    protected $appId;

    /**
     * @var string
     * @see \Qcloud_cos\Conf::SECRET_ID
     */
    protected $secretId;

    /**
     * @var string
     * @see \Qcloud_cos\Conf::SECRET_KEY
     */
    protected $secretKey;

    /**
     * {@inheritdoc}
     */
    public function write($file, $ext = '', $customName = '')
    {
        return $this->processWrite($file, $ext, $customName);
    }

    /**
     * {@inheritdoc}
     */
    protected function callUploadApi($file, $customName)
    {
        !$customName && $customName = $file;

        // NOTICE: 不以/开头,会返回错误"bucket与签名中的bucket不匹配"
        $dstPath = '/' . $customName;

        $ret = Cosapi::upload($file, $this->bucket, $dstPath);
        if ($ret['code'] === 0) {
            $ret['url'] = $ret['data']['access_url'];
        }

        return $ret;
    }
}
