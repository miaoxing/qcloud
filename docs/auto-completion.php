<?php

/**
 * @property    Miaoxing\Qcloud\Service\Cos $cos
 */
class CosMixin
{
}

/**
 * @mixin CosMixin
 */
class AutoCompletion
{
}

/**
 * @return AutoCompletion
 */
function wei()
{
    return new AutoCompletion();
}

/** @var Miaoxing\Qcloud\Service\Cos $cos */
$cos = wei()->cos;
