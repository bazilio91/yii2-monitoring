<?php

namespace bazilio\yii\monitoring\handlers;

use yii\base\Application;

class BaseHandler extends Handler
{
    public function bootstrap($app)
    {
        $app->on(
            Application::EVENT_BEFORE_REQUEST,
            function () use ($app) {
                \Yii::beginProfile('request', 'monitoring\timings');
            }
        );

        $app->on(
            Application::EVENT_BEFORE_ACTION,
            function () use ($app) {
                $this->transaction->name = $app->controller->id . '.' . $app->requestedAction->id;
                \Yii::beginProfile('action', 'monitoring\timings');
            }
        );

        $app->on(
            Application::EVENT_AFTER_ACTION,
            function () use ($app) {
                \Yii::endProfile('action', 'monitoring\timings');
            }
        );

        $app->on(
            Application::EVENT_AFTER_REQUEST,
            function () use ($app) {
                \Yii::endProfile('request', 'monitoring\timings');
            }
        );
    }
}