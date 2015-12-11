<?php


namespace bazilio\yii\monitoring;


use yii\log\Target;

class LogTarget extends Target
{
    public $module;
    public $categories = ['monitoring\*', 'yii\db\Command::query'];
    public $logVars = [];
    public $exportInterval = 0;

    /**
     * @param Monitoring $module
     * @param array $config
     */
    public function __construct($module, $config = [])
    {
        parent::__construct($config);
        $this->module = $module;
    }

    /**
     * Exports log [[messages]] to a specific destination.
     * Child classes must implement this method.
     */
    public function export()
    {
       $this->module->transaction->processLogs($this->messages);
    }
}