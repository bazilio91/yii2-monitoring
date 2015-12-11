<?php
/**
 * Application configuration shared by all applications and test types
 */

$config = [
    'bootstrap' => ['monitoring'],
    'runtimePath' => dirname(__DIR__) . '/_output',
    'components' => [
        'monitoring' => [
            'class' => 'bazilio\yii\monitoring\Monitoring'
        ]
    ],
    'params' => [
        'serverName' => 'localhost'
    ]
];

if (file_exists(__DIR__ . '/config-local.php')) {
    $config = yii\helpers\ArrayHelper::merge(
        $config,
        require(__DIR__ . '/config-local.php')
    );
}

return $config;
