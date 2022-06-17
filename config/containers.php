<?php

use app\services\ExchangeService;

/**
 * @return ExchangeService
 * @throws \yii\base\InvalidConfigException
 * @throws \yii\di\NotInstantiableException
 */
function exchangeService()
{
    return \Yii::$container->get('app\services\ExchangeService');
}