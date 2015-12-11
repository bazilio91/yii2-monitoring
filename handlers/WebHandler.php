<?php

namespace bazilio\yii\monitoring\handlers;

use yii\web\Application;
use yii\web\View;

class WebHandler extends BaseHandler
{
    public function bootstrap($app)
    {
        parent::bootstrap($app);

        if ($this->module->enableEndUser) {
            $app->on(
                Application::EVENT_BEFORE_ACTION,
                function () use ($app, &$agent) {
                    $app->controller->view->registerJs(
                        "console.log('start');",
                        View::POS_HEAD,
                        'rum-head'
                    );

                    $app->controller->view->registerJs(
                        "console.log('end');",
                        View::POS_END,
                        'rum-end'
                    );
                }
            );
        }
    }

}