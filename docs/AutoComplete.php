<?php

namespace MiaoxingDoc\Tencentyun {

    /**
     * @property    \Miaoxing\Tencentyun\Service\Tencentyun $tencentyun 腾讯云万象图片服务
     * @method      \Miaoxing\Tencentyun\Service\Tencentyun|\Miaoxing\Tencentyun\Service\Tencentyun[] tencentyun()
     *
     * @property    \Miaoxing\Tencentyun\Service\TencentyunCos $tencentyunCos 腾讯云对象存储服务
     * @method      \Miaoxing\Tencentyun\Service\TencentyunCos|\Miaoxing\Tencentyun\Service\TencentyunCos[] tencentyunCos()
     *
     * @property    \Miaoxing\Tencentyun\Service\TencentyunCosV5 $tencentyunCosV5 腾讯云对象存储服务V5
     * @method      \Miaoxing\Tencentyun\Service\TencentyunCosV5|\Miaoxing\Tencentyun\Service\TencentyunCosV5[] tencentyunCosV5()
     */
    class AutoComplete
    {
    }
}

namespace {

    /**
     * @return MiaoxingDoc\Tencentyun\AutoComplete
     */
    function wei()
    {
    }

    /** @var Miaoxing\Tencentyun\Service\Tencentyun $tencentyun */
    $tencentyun = wei()->tencentyun;

    /** @var Miaoxing\Tencentyun\Service\TencentyunCos $tencentyunCos */
    $tencentyunCos = wei()->tencentyunCos;

    /** @var Miaoxing\Tencentyun\Service\TencentyunCosV5 $tencentyunCosV5 */
    $tencentyunCosV5 = wei()->tencentyunCosV5;
}
