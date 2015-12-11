<?php

namespace bazilio\yii\monitoring\tests\unit;

use bazilio\yii\monitoring\Monitoring;
use bazilio\yii\monitoring\tests\TestCase;
use yii\base\Application;

class FirstTest extends TestCase
{
    protected function runAction($action, $params = [])
    {
        $controller = new FakeController('fake', \Yii::$app);
        \Yii::$app->trigger(Application::EVENT_BEFORE_REQUEST);
        \Yii::$app->controller = $controller;
        $controller->runAction($action, $params);
        \Yii::$app->getLog()->getLogger()->flush(true);
        \Yii::$app->trigger(Application::EVENT_AFTER_REQUEST);
    }

    protected function tearDown()
    {
        // prevent errors
        $this->getComponent()->logTarget->enabled = false;
        parent::tearDown();
    }


    /**
     * @return Monitoring
     */
    protected function getComponent()
    {
        return \Yii::$app->monitoring;
    }

    public function testOne()
    {
        $this->runAction('index');
        $m = $this->getComponent();
        \Yii::beginProfile('some fake db request', 'yii\db\Command::query');
        \Yii::endProfile('some fake db request', 'yii\db\Command::query');

        $m->send();

        $this->assertEquals('fake/index', $m->transaction->name);

        $this->assertArrayHasKey('action', $m->transaction->gauges);
        $this->assertArrayHasKey('total', $m->transaction->gauges);
        $this->assertArrayHasKey('memory', $m->transaction->gauges);
        $this->assertArrayHasKey('db', $m->transaction->gauges);
    }
}
