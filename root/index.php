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
    'twig.path' => __DIR__ . '/templates/ap',
) );
$app->register( new Silex\Provider\DoctrineServiceProvider(), array(
    'db.options' => array(
        'mysql_read' => array(
            'driver' => 'pdo_mysql',
            'host' => 'localhost',
            'dbname' => 'gaestebuch',
            'user' => 'root',
            'password' => 'XDrAgonStOrM129',
            'charset' => 'utf8'
        )
    )
) );

$app['debug'] = TRUE;

$app->mount( '/gb', new Guestbook\GuestbookControllerProvider() );
$app->mount( '/ap', new Adminpanel\AdminpanelControllerProvider() );

$app['session.storage.options'] = array(
    'lifetime' => 900
);

$app->before( function () use ( $app )
{
    $getPath = $app['request_context']->getPathInfo();

    //Wenn Session null dann true und führe aus, wenn Session nicht null dann false und führe nicht aus
    if ( !$app['session']->get( 'user' ) )
    {
        // Wenn Pfad = auth/login dann nicht redirecten
        if ( $getPath == '/ap/auth/login' || $getPath == '/ap/auth/reset' || $getPath == '/ap/auth/reset/code' )
        {
            return;
        }
        return $app->redirect( $app['url_generator']->generate( 'login' ) );
    }
} );

// Userzeile als Rückmeldung das er eingeloggt ist
//$userHeader = '<header><h3>Sie sind als  eingeloggt.</h3></header>';
//<a href="/php-gaestebuch/adminpanel' . $app['url_generator']->generate( 'settings' ) . '">' . $app['session']->get( 'user' ) . '</a>
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

//debug( 'Session Cookie: ', $app['session']->get( 'cookie_lifetime' ), ' Gesetzt bei:', $app['session']->get( 'user' ), PHP_EOL );
var_dump( $app['session']->get( 'cookie_lifetime' ) );

$app->run();
