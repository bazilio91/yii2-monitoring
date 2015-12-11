<?php

namespace bazilio\yii\monitoring;

use yii\base\Model;

class Transaction extends Model
{
    public $name;
    public $prefix;
    public $gauges = [];

    public function processLogs($messages)
    {
        $messages = \Yii::$app->getLog()->getLogger()->calculateTimings($messages);

        foreach ($messages as $message) {
            switch ($message['category']) {
                case 'monitoring\timings':
                    $this->gauges[$message['info']] = (int)($message['duration'] * 1000);
                    break;
                case 'yii\db\Command::query':
                    if (!isset($this->gauges['db'])) {
                        $this->gauges['db'] = 0;
                    }
                    $this->gauges['db'] += (int)($message['duration'] * 1000);
                    break;
            }
        }
    }
}