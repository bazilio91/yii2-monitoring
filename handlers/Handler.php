<?php

namespace bazilio\yii\monitoring\handlers;

use bazilio\yii\monitoring\Monitoring;
use bazilio\yii\monitoring\Transaction;
use yii\base\Component;

abstract class Handler extends Component
{
    /**
     * @var Monitoring
     */
    public $module;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * Bootstrap method to be called during application bootstrap stage.
     * @param \yii\base\Application $app the application currently running
     */
    abstract public function bootstrap($app);
}