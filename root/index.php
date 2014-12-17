<?php

error_reporting( E_ALL );
ini_set( 'log_errors', 1 );

// Dateien einbinden
require_once __DIR__ . '/../vendor/autoload.php';
include_once __DIR__ . '/../ControllerProviders/GuestbookControllerProvider.php';
include_once __DIR__ . '/../ControllerProviders/AdminpanelControllerProvider.php';

$app = new Silex\Application();

$app->register( new Silex\Provider\UrlGeneratorServiceProvider() );
$app->register( new Silex\Provider\SessionServiceProvider() );
$app->register( new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/templates'
) );
$app->register( new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'driver' => 'pdo_mysql',
        'dbname' => 'gaestebuch',
        'user' => 'root',
        'password' => 'XDrAgonStOrM129',
        'host' => 'localhost',
        'charset' => 'utf8',
    )
) );

$app['debug'] = TRUE;

$app->mount( '/gb', new Guestbook\GuestbookControllerProvider() );
$app->mount( '/ap', new Adminpanel\AdminpanelControllerProvider() );

$app['session.storage.options'] = array(
    'lifetime' => 900
);

//$user = new Guestbook\User( $app['db'] );
//$user->set('username', 'Patrickads');
//$user->set('useremail', 'asdsadsa@afdg.com');
//$user->set('password', 'adsfsgdhfj231233e');
//$user->set('role', 'Superman');
//$user->insert( 'user');

$app->before( function () use ( $app )
{
    $getPath = $app['request_context']->getPathInfo();

    //Wenn Session null dann true und führe aus, wenn Session nicht null dann false und führe nicht aus
    if ( !$app['session']->get( 'user' ) )
    {
        // Wenn Pfad = auth/login dann nicht redirecten
        if ( $getPath == '/ap/auth/login' || $getPath == '/ap/auth/reset' || $getPath == '/ap/auth/reset/code' || $getPath == '/gb' )
        {
            return;
        }
        return $app->redirect( $app['url_generator']->generate( 'login' ) );
    }
} );

// Mail Encoding auf UTF-8 setzen
mb_internal_encoding( "UTF-8" );

// Loginsession starten
$app['session']->start();

// Letzte Aktivität in der Session
$sessionLastUsed = $app['session']->getMetadataBag()->getLastUsed();

// Wenn nach 15 Minuten (900sek) keine Aktivität in der Session war und das Cookie Lifetime nicht 0 ist zerstöre diese
if ( ( $app['session']->get( 'cookie_lifetime' ) !== 0 ) && ( time() - $sessionLastUsed > 60 ) )
{
    session_destroy();
}

$app->run();
