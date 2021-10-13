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
            'on beforeSend' => function ($event) {
                yii::createObject([
                    'class' => yiier\helpers\ResponseHandler::class,
                    'event' => $event,
                ])->formatResponse();
            },
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
                        'DELETE <id>/user/<user>' => 'delete-user-banca', // Deletar um usuário de uma banca
                        'GET <id>/documento' => 'get-documents', // Listar todos os documentos de uma banca
                        'GET <id>/documento/<doc>' => 'get-document', // Listar um documentos de uma banca
                        'GET <id>/documento/<doc>/view' => 'view-document', // Visualizar um documentos de uma banca
                        'POST <id>/documento' => 'add-document', // Adicionar um documentos a uma banca
                        'DELETE <id>/documento/<doc>' => 'delete-document', // Deletar um documentos a uma banca
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['usuario-banca' => 'usuario-banca'], // CRUD Usuario-banca
                    'extraPatterns' => [
                        'GET id/<id_banca>/<id_usuario>' => 'id', // Pegar id do ub com id do user e banca
                        'POST <id>' => 'add', // Adicionar usuario na banca
                    ]
                ],
                'GET nota/<id_banca>' => 'usuario-banca/nota', // Pegar a nota final dado o id da banca
                'POST login' => 'login/login', // Realizar login
                'POST logout' => 'login/logout', // Realizar logouut
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
