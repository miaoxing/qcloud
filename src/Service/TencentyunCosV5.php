<?php

namespace Miaoxing\Tencentyun\Service;

use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\ServiceResponseException;
use Qcloud\Cos\Signature;

/**
 * 腾讯云对象存储服务V5
 *
 * @link https://github.com/tencentyun/cos-php-sdk-v5
 */
class TencentyunCosV5 extends Tencentyun
{
    /**
     * 应用ID
     */
    protected $appId;

    /**
     * @var string
     */
    protected $secretId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $domain;

    /**
     * @var Client
     */
    protected $client;

    public function signUrl($url, $seconds = '+10 minutes')
    {
        $url =  $this->getClient()->getObjectUrl($this->bucket, $url, $seconds);

        return $url;
    }

    public function getClient()
    {
        if (!$this->client) {
            $this->client = new Client([
                'region' => $this->region,
                'credentials' => [
                    'appId' => $this->appId,
                    'secretId' => $this->secretId,
                    'secretKey' => $this->secretKey,
                ],
            ]);
        }
        return $this->client;
    }

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

        try {
            /** @var \Guzzle\Service\Resource\Model $result */
            $result = $this->getClient()->putObject(array(
                'Bucket' => $this->bucket,
                'Key' => $customName,
                'Body' => fopen($file, 'rb'),
            ));
            if ($result->get('ObjectURL')) {
                return [
                    'code' => 0, // 兼容已有的接口
                    'url' => $this->domain . '/' . $customName,
                ];
            } else {
                return $this->err([
                    'message' => '请求失败',
                    'result' => $result,
                ]);
            }
        } catch (ServiceResponseException $e) {
            return $this->err($e->getMessage(), $e->getStatusCode());
        }
    }
}
