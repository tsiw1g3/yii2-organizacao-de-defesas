<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'Jn7jOniUIfgOTJGzTioxpzIlm7oHTDfm',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ]
        ],
        'loginUrl' => null,
        'response' => [
            'class' => 'yii\web\Response',
//            'on beforeSend' => function ($event) {
//                yii::createObject([
//                    'class' => yiier\helpers\ResponseHandler::class,
//                    'event' => $event,
//                ])->formatResponse();
//            },
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => false,
            'enableSession' => false,
            'loginUrl' => 403
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'session' => [
            'class' => 'yii\web\DbSession',
        ],
        'db' => $db,
        'formatter' => [
            'dateFormat' => 'dd.MM.yyyy',
            'decimalSeparator' => ',',
            'thousandSeparator' => ' ',
            'currencyCode' => 'BRL',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => true,
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['usuario' => 'usuario'], // CRUD Usuario
                    'extraPatterns' => [
                        'GET <id>/banca' => 'get-banca', // Listar todas as bancas de um usuario
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['banca' => 'banca'], // CRUD banca
                    'extraPatterns' => [
                        'GET <id>/users' =>'get-users', // Listar todos os usuarios de uma banca
                        'DELETE <id>/user/<user>' => 'delete-user-banca', // Listar todas as bancas de um usuario
                    ]
                ],
                'POST usuario-banca/<id>' => 'usuario-banca/add', // Adicionar usuario na banca
                'POST login' => 'login/login', // Realizar login
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

return $config;
