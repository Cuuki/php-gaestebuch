<?php

error_reporting( E_ALL );
ini_set( 'log_errors', 1 );

// Dateien einbinden
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/../lib/debug-functions.php';
include_once __DIR__ . '/../lib/dbconnect.php';
include_once __DIR__ . '/../lib/dbconfig.php';
$apFunctions = include_once __DIR__ . '/../lib/ap-functions.php';
$gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';
// Superuser anlegen
include_once __DIR__ . '/sudo-config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

define( 'ROUTES_DIR', realpath( __DIR__ . '/routes' ) );
define( 'USER_DIR', realpath( __DIR__ . '/user' ) );
define( 'POST_DIR', realpath( __DIR__ . '/post' ) );

// Mail Encoding auf UTF-8 setzen
mb_internal_encoding( "UTF-8" );

$app = new Silex\Application();
$app->register( new Silex\Provider\UrlGeneratorServiceProvider() );
$app->register( new Silex\Provider\SessionServiceProvider() );

$app['debug'] = TRUE;

$app['session.storage.options'] = array(
    'lifetime' => 900
);

$loader = new Twig_Loader_Filesystem( 'inc/templates' );
$twig = new Twig_Environment( $loader, array( 'debug' => true ) );

$app->before( function () use ( $app )
{
    $getPath = $app['request_context']->getPathInfo();

    // Wenn Session null dann true und führe aus, wenn Session nicht null dann false und führe nicht aus
    if ( !$app['session']->get( 'user' ) )
    {
        // Wenn Pfad = auth/login dann nicht redirecten
        if ( $getPath == '/auth/login' || $getPath == '/auth/reset' || $getPath == '/auth/reset/code' )
        {
            return;
        }
        return $app->redirect( $app['url_generator']->generate( 'login' ) );
    }
} );

$app->get( '/', function () use ( $app )
{
    return $app->redirect( 'user/dashboard/' );
} );

$app->get( '/post/', function () use ( $app )
{
    return $app->redirect( '../user/dashboard/' );
} );

$app->get( '/user/', function () use ( $app )
{
    return $app->redirect( 'dashboard/' );
} );

$app->get( '/auth/', function () use ( $app )
{
    return $app->redirect( 'login' );
} );

$app->get( '/auth/login', function () use ( $app, $twig )
{
    // Wenn bereits eingeloggt weiterleiten auf Dashboard
    if ( ($app['session']->get( 'user' )) != NULL )
    {
        return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
    }

    include_once ROUTES_DIR . '/auth/login.php';

    $render = $twig->render( 'login_form.html' );

    return $render;
} )->bind( 'login' );

$app->post( '/auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $db, $apFunctions )
{
    $processing = include_once ROUTES_DIR . '/auth/processing_login.php';

    return $processing;
} );

$app->get( '/auth/reset', function () use ( $app, $twig, $db, $apFunctions )
{
    // Wenn bereits eingeloggt weiterleiten auf Dashboard
    if ( ($app['session']->get( 'user' )) != NULL )
    {
        return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
    }

    include_once ROUTES_DIR . '/auth/reset.php';

    $render = $twig->render( 'reset_form.html' );

    return $render;
} );

$app->post( '/auth/reset', function ( Request $email ) use ( $db, $app )
{
    $processing = include_once ROUTES_DIR . '/auth/processing_reset.php';

    return $processing;
} );

$app->get( 'auth/reset/code', function () use ( $app, $twig )
{
    // Wenn bereits eingeloggt weiterleiten auf Dashboard
    if ( ($app['session']->get( 'user' )) != NULL )
    {
        return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
    }

    include_once ROUTES_DIR . '/auth/code.php';

    $render = $twig->render( 'code_form.html' );

    return $render;
} );

$app->post( 'auth/reset/code', function ( Request $code, Request $password ) use ( $db, $app )
{
    $processing = include_once ROUTES_DIR . '/auth/processing_code.php';

    return $processing;
} );

// User Einstellungen
$app->get( '/user/dashboard/settings', function () use ( $app, $twig )
{
    include_once USER_DIR . '/settings.php';

    $render = $twig->render( 'settings_form.html' );

    return $render;
} )->bind( 'settings' );

// Userzeile als Rückmeldung das er eingeloggt ist
$userHeader = '<header><h3>Sie sind als <a href="/php-gaestebuch/adminpanel' .
        $app['url_generator']->generate( 'settings' ) . '">' . $app['session']->get( 'user' ) . '</a> eingeloggt.</h3></header>';

$app->get( '/user/dashboard/', function () use ( $app, $twig, $userHeader )
{
    $loggedInSince = $app['session']->get( 'time' );

    include_once USER_DIR . '/dashboard.php';

    $render = $twig->render( 'dashboard_form.html' );

    return new Response( $userHeader . '<p>Sie sind eingeloggt seid: ' . date( 'h:i:sa, d.m.Y', $loggedInSince ) . '</p>' . $render, 201 );
} )->bind( 'dashboard' );

$app->get( '/user/dashboard/settings/username', function () use ( $app, $twig )
{
    // Alter Benutzername, Neuer Benutzername einbinden
    include_once USER_DIR . '/dashboard/settings_username.php';

    $render = $twig->render( 'settings_update_form.html', array(
        'oldinput_for' => 'oldusername',
        'oldinput_text' => 'Alter Benutzername:',
        'oldinput_name' => 'oldusername',
        'newinput_for' => 'username',
        'newinput_text' => 'Neuer Benutzername:',
        'newinput_name' => 'username'
            ) );

    return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
} )->bind( 'changeUsername' );

$app->post( '/user/dashboard/settings/username', function ( Request $username ) use ( $db, $app, $apFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_settings_username.php';

    return $processing;
} );

$app->get( '/user/dashboard/settings/password', function () use ( $app, $twig )
{
    // Altes Passwort, Neues Passwort einbinden
    include_once USER_DIR . '/dashboard/settings_password.php';

    $render = $twig->render( 'settings_update_form.html', array(
        'oldinput_for' => 'oldpassword',
        'oldinput_text' => 'Altes Passwort:',
        'oldinput_name' => 'oldpassword',
        'newinput_for' => 'password',
        'newinput_text' => 'Neues Passwort:',
        'newinput_name' => 'password'
            ) );

    return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
} )->bind( 'changePassword' );

$app->post( '/user/dashboard/settings/password', function ( Request $password ) use ( $db, $app, $apFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_settings_password.php';

    return $processing;
} );

$app->get( '/user/dashboard/settings/email', function () use ( $app, $twig )
{
    // Alte E-Mail, Neue E-Mail einbinden
    include_once USER_DIR . '/dashboard/settings_email.php';

    $render = $twig->render( 'settings_update_form.html', array(
        'oldinput_for' => 'oldemail',
        'oldinput_text' => 'Alte E-Mail Adresse:',
        'oldinput_name' => 'oldemail',
        'newinput_for' => 'email',
        'newinput_text' => 'Neue E-Mail Adresse:',
        'newinput_name' => 'email'
            ) );

    return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
} )->bind( 'changeEmail' );

$app->post( '/user/dashboard/settings/email', function ( Request $email ) use ( $db, $app, $apFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_settings_email.php';

    return $processing;
} );

// Benutzer hinzufügen
$app->get( '/user/dashboard/add', function () use ( $app, $twig, $userHeader )
{
    include_once USER_DIR . '/dashboard/add.php';

    $render = $twig->render( 'user_form.html', array(
        'headline' => 'Benutzer hinzufügen:',
        'submitvalue' => 'Anlegen',
        'link_back' => '../'
            ) );

    return new Response( $userHeader . $render, 201 );
} )->bind( 'add' );

$app->post( '/user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_add.php';

    return $processing;
} );

// Benutzerdaten bearbeiten
$app->get( '/user/dashboard/update/', function () use ( $app, $db, $apFunctions, $userHeader )
{
    $processing = include_once USER_DIR . '/dashboard/display_update.php';

    return $processing;
} )->bind( 'update' );

$app->get( '/user/dashboard/update/{id}', function ( $id ) use ( $app, $twig, $db, $apFunctions, $gbFunctions, $userHeader )
{
    include_once USER_DIR . '/dashboard/display_update_id.php';

    $render = $twig->render( 'user_form.html', array(
        'headline' => 'Alle Benutzerdaten ändern:',
        'submitvalue' => 'Ändern',
        'link_back' => '../update'
            ) );

    return new Response( $userHeader . $displayUser . $render, 201 );
} );

$app->post( '/user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_update_id.php';

    return $processing;
} );

$app->get( '/user/dashboard/update/{id}/username', function ( $id ) use ( $app, $twig, $db, $userHeader )
{
    include_once USER_DIR . '/dashboard/update_username.php';

    $render = $twig->render( 'user_update_form.html', array(
        'label_for' => 'username',
        'label_text' => 'Neuer Benutzername:',
        'input_name' => 'username'
            ) );

    return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
} );

$app->post( '/user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $db, $apFunctions, $gbFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_update_username.php';

    return $processing;
} );

$app->get( '/user/dashboard/update/{id}/email', function ( $id ) use ( $app, $twig, $db, $userHeader )
{
    include_once USER_DIR . '/dashboard/update_email.php';

    $render = $twig->render( 'user_update_form.html', array(
        'label_for' => 'useremail',
        'label_text' => 'Neue E-Mail Adresse:',
        'input_name' => 'useremail'
            ) );

    return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
} );

$app->post( '/user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $db, $apFunctions, $gbFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_update_email.php';

    return $processing;
} );

$app->get( '/user/dashboard/update/{id}/password', function ( $id ) use ( $app, $twig, $db, $userHeader )
{
    include_once USER_DIR . '/dashboard/update_password.php';

    $render = $twig->render( 'user_update_form.html', array(
        'label_for' => 'password',
        'label_text' => 'Neues Passwort:',
        'input_name' => 'password'
            ) );

    return new Response( $userHeader . $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
} );

$app->post( '/user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_update_password.php';

    return $processing;
} );

// Benutzer löschen
$app->get( '/user/dashboard/delete/', function () use ( $app, $db, $apFunctions, $userHeader )
{
    $processing = include_once USER_DIR . '/dashboard/display_delete.php';

    return $processing;
} )->bind( 'delete' );

$app->get( '/user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
{
    $processing = include_once USER_DIR . '/dashboard/processing_delete.php';

    return $processing;
} );

// Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
$app->get( '/post/add', function () use ( $app )
{
    return $app->redirect( '../../#add' );
} );

$app->get( '/post/update', function () use ( $db, $app, $userHeader )
{
    include_once POST_DIR . '/display_update.php';

    include_once POST_DIR . '/update.php';

    return new Response( $userHeader . displayPagination( $currentpage, $totalpages ) . displayUpdateEntries( $posts ) .
            '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
} )->bind( 'postUpdate' );

$app->get( '/post/update/{id}', function ( $id ) use ( $db, $app, $twig, $gbFunctions, $userHeader )
{
    include_once POST_DIR . '/update.php';

    $entryData = getEntry( $db, $id );

    $render = $twig->render( 'post_form.html' );

    return new Response( $userHeader . displayPosts( $entryData ) . $render, 201 );
} );

$app->post( '/post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $app, $gbFunctions, $userHeader )
{
    $processing = include_once POST_DIR . '/processing_update.php';

    return $processing;
} );

$app->get( '/post/delete', function () use ( $db, $app, $userHeader )
{
    include_once POST_DIR . '/display_delete.php';

    include_once POST_DIR . '/delete.php';

    return new Response( $userHeader . displayPagination( $currentpage, $totalpages ) . displayDeleteEntries( $posts ) .
            '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
} )->bind( 'deletePost' );

$app->get( '/post/delete/{id}', function ( $id ) use ( $db, $app )
{
    $processing = include_once POST_DIR . '/processing_delete.php';

    return $processing;
} );

// Ausloggen
$app->get( '/user/dashboard/logout', function () use ( $app )
{
    session_destroy();

    // nach ausloggen weiterleiten auf loginseite
    return $app->redirect( $app['url_generator']->generate( 'login' ) );
} )->bind( 'logout' );

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
