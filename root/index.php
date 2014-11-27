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

$app['debug'] = TRUE;

$app->mount( '/gb', new Guestbook\GuestbookControllerProvider() );
$app->mount( '/ap', new Adminpanel\AdminpanelControllerProvider() );

$app['session.storage.options'] = array(
    'lifetime' => 900
);

// Mail Encoding auf UTF-8 setzen
mb_internal_encoding( "UTF-8" );

// Loginsession starten
$app['session']->start();

// Letzte Aktivität in der Session
$sessionLastUsed = $app['session']->getMetadataBag()->getLastUsed();

// Wenn nach 15 Minuten (900sek) keine Aktivität in der Session war und das Cookie Lifetime nicht 0 ist zerstöre diese
if ( ( $app['session']->get( 'cookie_lifetime' ) !== 0 ) && ( time() - $sessionLastUsed > 900 ) )
{
    session_destroy();
}

var_dump( $app['session']->get( 'cookie_lifetime' ) );

$app->run();
