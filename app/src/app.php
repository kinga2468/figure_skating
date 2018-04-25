<?php
/**
 * Init application.
 */
use Silex\Application;
use Silex\Provider\AssetServiceProvider;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Silex\Provider\DoctrineServiceProvider;
use Silex\Provider\FormServiceProvider;
use Silex\Provider\ValidatorServiceProvider;
use Silex\Provider\SessionServiceProvider;
use Silex\Provider\SecurityServiceProvider;

$app = new Application();

$app->register(new AssetServiceProvider());
$app->register(
    new TwigServiceProvider(),
    [
        'twig.path' => dirname(dirname(__FILE__)).'/templates',
    ]
);

$app->register(new LocaleServiceProvider());
$app->register(
    new TranslationServiceProvider(),
    [
        'locale' => 'pl',
        'locale_fallbacks' => array('en'),
    ]
);
$app->extend('translator', function ($translator, $app) {
    $translator->addResource('xliff', __DIR__.'/../translations/messages.en.xml', 'en', 'messages');
    $translator->addResource('xliff', __DIR__.'/../translations/validators.en.xml', 'en', 'validators');
    $translator->addResource('xliff', __DIR__.'/../translations/messages.pl.xml', 'pl', 'messages');
    $translator->addResource('xliff', __DIR__.'/../translations/validators.pl.xml', 'pl', 'validators');

    return $translator;
});
$app->register(
    new DoctrineServiceProvider(),
    [
        'db.options' => [
            'driver'    => 'pdo_mysql',
            'host'      => 'localhost',
            'dbname'    => 'baza',
            'user'      => 'root',
            'password'  => '',
            'charset'   => 'utf8',
            'collate' => 'utf8mb4_unicode_ci',
            'driverOptions' => [
                1002 => 'SET NAMES utf8',
            ],
        ],
    ]
);

$app->register(new FormServiceProvider());
$app->register(new ValidatorServiceProvider());
$app->register(new SessionServiceProvider());

$app->register(
    new SecurityServiceProvider(),
    [
        'security.firewalls' => [
            'dev' => [
                'pattern' => '^/(_(profiler|wdt)|css|images|js)/',
                'security' => false,
            ],
            'main' => [
                'pattern' => '^.*$',
                'form' => [
                    'login_path' => 'auth_login',
                    'check_path' => 'auth_login_check',
                    'default_target_path' => 'main_index',
                    'username_parameter' => 'login_type[login]',
                    'password_parameter' => 'login_type[password]',
                ],
                'anonymous' => true,
                'logout' => [
                    'logout_path' => 'auth_logout',
                    'target_url' => 'main_index',
                ],
                'users' => function () use ($app) {
                    return new Provider\UserProvider($app['db']);
                },
            ],
        ],
        'security.access_rules' => [
            ['^/auth.+$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
            ['^/user.+$', 'ROLE_ADMIN'],
            ['^/.+$', 'ROLE_USER'],
//            ['^/.+$', 'ROLE_ADMIN'],
//            ['^/user.+$', 'ROLE_ADMIN'],
//            ['^/.+$', 'IS_AUTHENTICATED_ANONYMOUSLY'],
        //to pod spodem odkomentuj żeby działało, to nad nie odkomentowuj
//            ['.+/delete$', 'ROLE_ADMIN'],
//            ['.+/edit$', 'ROLE_ADMIN'],
//            ['.+/add$', 'ROLE_ADMIN'],
        ],
        'security.role_hierarchy' => [
            'ROLE_ADMIN' => ['ROLE_USER'],
        ],
    ]
);

//dump($app['security.encoder.bcrypt']->encodePassword('kinga-admin', ''));
//dump($app['security.encoder.bcrypt']->encodePassword('kinga-user', ''));

return $app;