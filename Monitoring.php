<?php
namespace bazilio\yii\monitoring;

use bazilio\yii\monitoring\handlers\BaseHandler;
use bazilio\yii\monitoring\handlers\ConsoleHandler;
use bazilio\yii\monitoring\handlers\WebHandler;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\Component;

/**
 * Class Newrelic
 * @package bazilio\yii\newrelic
 */
class Monitoring extends Component implements BootstrapInterface
{
    /**
     * @var bool Enable agent
     */
    public $enabled = true;
    /**
     * @var string App name
     */
    public $name;
    /**
     * @var string handlers\Handler
     */
    public $handler;
    /**
     * @var bool Enable view instrumentation with newrelic scripts
     */
    public $enableEndUser = true;
    /**
     * @var LogTarget
     */
    public $logTarget;

    /**
     * @var Transaction
     */
    public $transaction;

    /**
     * @inheritdoc
     */
    public function bootstrap($app)
    {
        if (!$this->enabled) {
            return;
        }

        $this->logTarget = \Yii::$app->getLog()->targets['monitoring'] = new LogTarget($this);

        if ($this->handler) {
            $handler = new $this->handler();
        } elseif ($app instanceof \yii\web\Application) {
            $handler = new WebHandler();
        } elseif ($app instanceof \yii\console\Application) {
            $handler = new ConsoleHandler();
        } else {
            $handler = new BaseHandler();
        }
        $handler->module = $this;
        $handler->bootstrap($app);
        $this->transaction = new Transaction();
        $handler->transaction = &$this->transaction;

        if (!defined('YII_MONITORING_TEST')) {
            $app->on(Application::EVENT_AFTER_REQUEST, [$this, 'send']);
        }
    }

    public function init()
    {
        parent::init();
        if ($this->enabled) {
            $this->name = $this->name ? $this->name : \Yii::$app->name;
        }
    }

    public function send()
    {
        \Yii::$app->getLog()->getLogger()->flush();
        $this->logTarget->export();
        $this->transaction->gauges['total'] = \Yii::$app->getLog()->getLogger()->getElapsedTime();
        $this->transaction->gauges['memory'] = memory_get_peak_usage(true);

        $connection = new \Domnikl\Statsd\Connection\UdpSocket('127.0.0.1', 8125);
        $statsd = new \Domnikl\Statsd\Client($connection, $this->transaction->name);
        $statsd->startBatch();
        $statsd->increment('hits', 1);
        foreach ($this->transaction->gauges as $k => $v) {
            $statsd->gauge($k, $v);
        }
        $statsd->endBatch();

    }
}