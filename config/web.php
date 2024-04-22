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
            ],
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
            'identityClass' => 'app\models\Usuario',
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
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.gmail.com',
                'username' => 'sistemadedefesasufba@gmail.com',
                'password' => $_ENV["MAIL_PASSWORD"],
                'port' => '587',
                'encryption' => 'tls',
            ],             
            'useFileTransport' => empty($_ENV["MAIL_PASSWORD"]),
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
            'timeout' => 3600
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
                        'OPTIONS' => 'allow-cors',
                        'GET' => 'get-usuarios',
                        'OPTIONS <id>' => 'allow-cors',
                        'POST <id>' => 'view',
                        'POST <id>/role' => 'edit-role',
                        'PUT <id>' => 'edit-usuario',
                        'OPTIONS <id>/role' => 'allow-cors',
                        'OPTIONS <id>/invite' => 'allow-cors',
                        'GET <id>/banca' => 'get-banca', // Listar todas as bancas de um usuario
                        'OPTIONS <id>/banca' => 'allow-cors', // Listar todas as bancas de um usuario
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['invite' => 'invite'], // CRUD Usuario
                    'extraPatterns' => [
                        'OPTIONS' => 'allow-cors',
                        'OPTIONS <id>' => 'allow-cors',
                        'GET <hash>' => 'get-invite',
                        'OPTIONS <id>' => 'allow-cors',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['banca' => 'banca'], // CRUD banca
                    'extraPatterns' => [
                        'OPTIONS' => 'allow-cors',
                        'OPTIONS <id>' => 'allow-cors',
                        'GET <id>' => 'get-banca',
                        'GET <id>/users' => 'get-users', // Listar todos os usuarios de uma banca
                        'OPTIONS <id>/users' => 'allow-cors', // Listar todos os usuarios de uma banca
                        'DELETE <id>/user/<user>' => 'delete-user-banca', // Deletar um usuário de uma banca
                        'OPTIONS <id>/user/<user>' => 'allow-cors', // Deletar um usuário de uma banca
                        'GET <id>/documento' => 'get-documents', // Listar todos os documentos de uma banca
                        'OPTIONS <id>/documento' => 'allow-cors', // Listar todos os documentos de uma banca
                        'GET <id>/documento/<doc>' => 'get-document', // Listar um documentos de uma banca
                        'GET <id>/documento/<doc>/view' => 'view-document', // Visualizar um documentos de uma banca
                        'OPTIONS <id>/documento/<doc>/view' => 'allow-cors', // Visualizar um documentos de uma banca
                        'POST <id>/documento' => 'add-document', // Adicionar um documentos a uma banca
                        'DELETE <id>/documento/<doc>' => 'delete-document', // Deletar um documentos a uma banca
                        'OPTIONS <user_id>/bancas' => 'allow-cors',
                        'GET <user_id>/bancas' => 'get-bancas-by-user', // Listar todas as bancas de um usuário
                        'DELETE <id>/delete' => 'delete-banca', // Listar todas as bancas de um usuário
                        'OPTIONS <id>/delete' => 'allow-cors', // Deletar uma banca
                        'OPTIONS <id_banca>/report' => 'allow-cors', // Gerar o relatorio
                        'POST <id_banca>/report' => 'get-report', // Gerar o relatorio
                        'OPTIONS <id_banca>/reportInfo' => 'allow-cors', // Pegar informacoes para gerar relatorio
                        'GET <id_banca>/reportInfo' => 'report-info', // Pegar informacoes para gerar relatorio
                        'GET' => 'get-bancas', // Pegar todas as bancas de todos os usuários
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['usuario-banca' => 'usuario-banca'], // CRUD Usuario-banca
                    'extraPatterns' => [
                        'OPTIONS' => 'allow-cors',
                        'OPTIONS <id>' => 'allow-cors',
                        'GET id/<id_banca>/<id_usuario>' => 'id', // Pegar id do ub com id do user e banca
                        'OPTIONS id/<id_banca>/<id_usuario>' => 'allow-cors', // Pegar id do ub com id do user e banca
                        'POST <id>' => 'add', // Adicionar usuario na banca
                        'GET usuarios/<id_banca>' => 'usuarios-banca-by-banca',
                        'OPTIONS usuarios/<id_banca>' => 'allow-cors',
                        'POST usuarios/email' => 'send-email', // Envio de emails para convite
                        'OPTIONS nota/<id_banca>/<id_user>' => 'allow-cors', // Dar nota para a banca
                        'POST nota/<id_banca>/<id_user>' => 'give-score', // Dar nota para a banca
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['google-calendar' => 'google-calendar'], // CRUD google-calendar
                    'extraPatterns' => [
                        'OPTIONS' => 'allow-cors',
                        'GET auth' => 'auth',
                        'OPTIONS create' => 'allow-cors',
                        'POST create' => 'create-event',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['reset-password' => 'reset-password'], // CRUD google-calendar
                    'extraPatterns' => [
                        'OPTIONS' => 'allow-cors',
                        'POST' => 'create',
                        'GET <hash>' => 'get-reset-hash',
                        'OPTIONS <hash>' => 'allow-cors',
                        'POST reset' => 'reset',
                        'OPTIONS reset' => 'allow-cors',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['documento' => 'documento'], // CRUD google-calendar
                    'extraPatterns' => [                        
                        'POST <id_banca>' => 'get-doc', // Gerar o relatorio
                        'OPTIONS <id_banca>' => 'allow-cors', // Gerar o relatorio
                        'GET participacao/<id_banca>' => 'get-doc-participacao', // Gerar documento de participação na banca.
                        'OPTIONS participacao/<id_banca>' => 'allow-cors', // Gerar documento de participação na banca.
                        'GET orientacao/<id_banca>' => 'get-doc-orientacao', // Gerar documento de orientação na banca.
                        'OPTIONS orientacao/<id_banca>' => 'allow-cors', // Gerar documento de orientação na banca.
                        'GET documentoInfo/<id_banca>' => 'documento-info', // Pegar informacoes para gerar relatorio
                        'OPTIONS documentoInfo/<id_banca>' => 'allow-cors', // Pegar informacoes para gerar relatorio
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['cursos' => 'curso'], // CRUD cursos
                    'extraPatterns' => [                        
                        'GET' => 'get-cursos', // Obter lista de cursos
                        'OPTIONS' => 'allow-cors',
                        'POST' => 'create-curso', // Cria um curso
                        'OPTIONS' => 'allow-cors',
                        'PUT <id>' => 'edit-cursos', // Editar um curso a partir de seu ID
                        'DELETE <id>' => 'delete-curso', // Editar um curso a partir de seu ID
                        'OPTIONS <id_banca>' => 'allow-cors', // Editar um curso a partir de seu ID
                    ]
                ],
                'GET nota/<id_banca>' => 'usuario-banca/nota', // Pegar a nota final dado o id da banca
                'OPTIONS nota/<id_banca>' => 'usuario-banca/allow-cors', // Pegar a nota final dado o id da banca
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
