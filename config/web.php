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
                'port'=>'465',
                'encryption'=>'ssl',
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
            'baseUrl' => $_ENV["BASE_URL"],
            'rules' => [
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['usuario' => 'usuario'], // CRUD Usuario
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'GET' => 'get-usuarios',
                        'OPTIONS <id>' => 'options',
                        'OPTIONS pre-cadastro' => 'options',
                        'POST pre-cadastro' => 'pre-register',
                        'POST <id>' => 'view',
                        'POST <id>/role' => 'edit-role',
                        'PUT <id>' => 'edit-usuario',
                        'OPTIONS <id>/role' => 'options',
                        'OPTIONS <id>/invite' => 'options',
                        'GET <id>/banca' => 'get-banca', // Listar todas as bancas de um usuario
                        'OPTIONS <id>/banca' => 'options', // Listar todas as bancas de um usuario
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['invite' => 'invite'], // CRUD Usuario
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'OPTIONS <id>' => 'options',
                        'GET <hash>' => 'get-invite',
                        'OPTIONS <id>' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule', 'controller' =>
                    ['banca' => 'banca'], // CRUD banca
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'OPTIONS <id>' => 'options',
                        'GET <id>' => 'get-banca',
                        'GET <id>/users' => 'get-users', // Listar todos os usuarios de uma banca
                        'OPTIONS <id>/users' => 'options', // Listar todos os usuarios de uma banca
                        'DELETE <id>/user/<user>' => 'delete-user-banca', // Deletar um usuário de uma banca
                        'OPTIONS <id>/user/<user>' => 'options', // Deletar um usuário de uma banca
                        'GET <id>/documento' => 'get-documents', // Listar todos os documentos de uma banca
                        'OPTIONS <id>/documento' => 'options', // Listar todos os documentos de uma banca
                        'GET <id>/documento/<doc>' => 'get-document', // Listar um documentos de uma banca
                        'GET <id>/documento/<doc>/view' => 'view-document', // Visualizar um documentos de uma banca
                        'OPTIONS <id>/documento/<doc>/view' => 'options', // Visualizar um documentos de uma banca
                        'POST <id>/documento' => 'add-document', // Adicionar um documentos a uma banca
                        'DELETE <id>/documento/<doc>' => 'delete-document', // Deletar um documentos a uma banca
                        'OPTIONS <user_id>/bancas' => 'options',
                        'GET <user_id>/bancas' => 'get-bancas-by-user', // Listar todas as bancas de um usuário
                        'DELETE <id>/delete' => 'delete-banca', // Listar todas as bancas de um usuário
                        'OPTIONS <id>/delete' => 'options', // Deletar uma banca
                        'OPTIONS <id_banca>/report' => 'options', // Gerar o relatorio
                        'POST <id_banca>/report' => 'get-report', // Gerar o relatorio
                        'OPTIONS <id_banca>/reportInfo' => 'options', // Pegar informacoes para gerar relatorio
                        'GET <id_banca>/reportInfo' => 'report-info', // Pegar informacoes para gerar relatorio
                        'GET' => 'get-bancas', // Pegar todas as bancas de todos os usuários
                        'PUT visibilidade/<id>' => 'update-visibility', // Altera a visibilidade da banca
                        'OPTIONS visibilidade/<id>' => 'options', // Altera a visibilidade da banca
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['usuario-banca' => 'usuario-banca'], // CRUD Usuario-banca
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'OPTIONS <id>' => 'options',
                        'GET id/<id_banca>/<id_usuario>' => 'id', // Pegar id do ub com id do user e banca
                        'OPTIONS id/<id_banca>/<id_usuario>' => 'options', // Pegar id do ub com id do user e banca
                        'POST <id>' => 'add', // Adicionar usuario na banca
                        'GET usuarios/<id_banca>' => 'usuarios-banca-by-banca',
                        'OPTIONS usuarios/<id_banca>' => 'options',
                        'POST usuarios/email' => 'send-email', // Envio de emails para convite
                        'OPTIONS nota/<id_banca>/<id_user>' => 'options', // Dar nota para a banca
                        'POST nota/<id_banca>/<id_user>' => 'give-score', // Dar nota para a banca
                        'POST notas/<id_banca>' => 'give-score-in-batch', // Dar nota para a banca em lote
                        'OPTIONS notas/<id_banca>' => 'options', // Dar nota para a banca em lote
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['google-calendar' => 'google-calendar'], // CRUD google-calendar
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'GET auth' => 'auth',
                        'OPTIONS create' => 'options',
                        'POST create' => 'create-event',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['reset-password' => 'reset-password'], // CRUD google-calendar
                    'extraPatterns' => [
                        'OPTIONS' => 'options',
                        'POST' => 'create',
                        'GET <hash>' => 'get-reset-hash',
                        'OPTIONS <hash>' => 'options',
                        'POST reset' => 'reset',
                        'OPTIONS reset' => 'options',
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['documento' => 'documento'], // CRUD google-calendar
                    'extraPatterns' => [                        
                        'OPTIONS' => 'options',
                        'OPTIONS <id_banca>' => 'options',
                        'POST <id_banca>' => 'get-doc', // Gerar o relatorio
                        'OPTIONS <id_banca>' => 'options', // Gerar o relatorio
                        'GET participacao/<id_banca>' => 'get-doc-participacao', // Gerar documento de participação na banca.
                        'OPTIONS participacao/<id_banca>' => 'options', // Gerar documento de participação na banca.
                        'GET orientacao/<id_banca>' => 'get-doc-orientacao', // Gerar documento de orientação na banca.
                        'OPTIONS orientacao/<id_banca>' => 'options', // Gerar documento de orientação na banca.
                        'GET documentoInfo/<id_banca>' => 'documento-info', // Pegar informacoes para gerar relatorio
                        'OPTIONS documentoInfo/<id_banca>' => 'options', // Pegar informacoes para gerar relatorio
                    ]
                ],
                [
                    'class' => 'yii\rest\UrlRule',
                    'controller' => ['cursos' => 'curso'], // CRUD cursos
                    'extraPatterns' => [                        
                        'OPTIONS' => 'options',
                        'GET' => 'get-cursos', // Obter lista de cursos
                        'POST' => 'create-curso', // Cria um curso
                        'OPTIONS <id_banca>' => 'options', // Editar um curso a partir de seu ID
                        'PUT <id>' => 'edit-cursos', // Editar um curso a partir de seu ID
                        'DELETE <id>' => 'delete-curso', // Editar um curso a partir de seu ID
                    ]
                ],
                'OPTIONS nota/<id_banca>' => 'usuario-banca/options', // Pegar a nota final dado o id da banca
                'GET nota/<id_banca>' => 'usuario-banca/nota', // Pegar a nota final dado o id da banca
                'OPTIONS login' => 'login/options', // Realizar login
                'POST login' => 'login/login', // Realizar login
                'DELETE login' => 'login/refresh-token', // Realizar logouut
            ],
        ],
        'jwt' => [
            'class' => \sizeg\jwt\Jwt::class,
            'key' => 'jUELuvknwfL2lyFTSbiv4zgKxqsxHflq',
            'jwtValidationData' => \app\components\JwtValidationData::class,
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
