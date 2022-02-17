<?php

namespace MiaoxingTest\Qcloud\Service;

use Miaoxing\Plugin\Test\BaseTestCase;
use Miaoxing\Qcloud\Service\Cos;
use Qcloud\Cos\Client;

class CosTest extends BaseTestCase
{
    public function testGetClient()
    {
        if (!$this->wei->getConfig('cos')) {
            $this->wei->setConfig('cos', [
                'region' => 'ap-shanghai',
                'secretId' => 'secretId',
                'secretKey' => 'secretKey',
                'bucket' => 'bucket',
            ]);
        }

        $client = Cos::getClient();
        $this->assertInstanceOf(Client::class, $client);
    }
}
