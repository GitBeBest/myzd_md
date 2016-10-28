<?php
/**
 * Created by PhpStorm.
 * User: pengcheng
 * Date: 2016/10/9
 * Time: 15:29
 */
$params = require(__DIR__ . '/params.php');

$config = [
    'id' => 'basic',
    'defaultRoute' => 'mobileDoctor/home/index',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '6EJnnt37pOI5rMav9pgDkrhhfYKGZfCn',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\user\UserIdentity',
            'enableAutoLogin' => true,
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
                    'levels' => ['error', 'warning','info'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/dbdev.php'),
        'db2' => require(__DIR__.'/db2.php'),
        'mongodb' => [
            'class' => 'yii\mongodb\Connection',
            'dsn' => 'mongodb://120.26.107.48:27017/myzd'
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
                '<controller:\w+>/<action:index>' => '<controller>/index',
                '<controller:\w+>/<id:\d+>' => '<controller>/view',
                '<controller:\w+>/<action:\w+><id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/<action:\w+>/<\w(-/)>' => '<controller>/<action>',
            ],
        ],

        'view' => [
            'theme' => [
                'pathMap' => [
                    '@app/modules/mobileDoctor/views/' => '@app/views/themes/md2/',
                    '@app/modules/doctor/views/' => '@app/views/themes/md2/',
                ],
                'basePath' => '@app/views/themes/md2',
                'baseUrl' => '@app/themes/basic',
            ],
        ],
        'image' => array(
            'class' => 'application.extensions.image.CImageComponent',
            'driver' => 'GD',
        ),
    ],
    'params' => $params,
    'modules' => [
        'doctor' => [
            'class' => 'app\modules\doctor\DoctorModule'
        ],
        'mobileDoctor' => [
            'class' => 'app\modules\mobileDoctor\mobileDoctorModule'
        ],
        'translate' => [
            'class' => 'app\modules\translate\TranslateModule'
        ],
        'weiXinPub' => [
            'class' => 'app\modules\weixinpub\weiXinPubModule'
        ]
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;