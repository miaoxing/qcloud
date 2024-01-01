<?php

namespace Miaoxing\Qcloud\Service;

use Miaoxing\Plugin\Service\BaseStorage;
use Qcloud\Cos\Client;
use Qcloud\Cos\Exception\ServiceResponseException;
use Wei\Ret;

class Cos extends BaseStorage
{
    /**
     * @var string
     */
    protected $region;

    /**
     * @var string
     */
    protected $secretId;

    /**
     * @var string
     */
    protected $secretKey;

    /**
     * @var string|null
     */
    protected $token;

    /**
     * @var string
     */
    protected $bucket;

    /**
     * @var string|null
     */
    protected $baseUrl;

    /**
     * @var Client
     */
    protected $client;

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function write(string $path, string $content, array $options = []): Ret
    {
        try {
            $this->getClient()->upload($this->bucket, $path, $content);
            return suc();
        } catch (ServiceResponseException $e) {
            return err([
                'message' => $e->getMessage(),
                'code' => $e->getStatusCode(),
                'cosErrorCode' => $e->getCosErrorCode(),
            ]);
        } catch (\Exception $e) {
            return err($e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     * @svc
     */
    protected function getUrl(string $path): string
    {
        $path = '/' . ltrim($path, '/');
        if ($this->baseUrl) {
            return $this->baseUrl . $path;
        }
        return 'https://' . $this->bucket . '.cos.' . $this->region . '.myqcloud.com' . $path;
    }

    /**
     * @svc
     */
    protected function getClient(): Client
    {
        if (!$this->client) {
            $this->client = new Client([
                'region' => $this->region,
                'credentials' => [
                    'secretId' => $this->secretId,
                    'secretKey' => $this->secretKey,
                    'token' => $this->token,
                ],
            ]);
        }
        return $this->client;
    }
}
