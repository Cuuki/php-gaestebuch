<?php

error_reporting( E_ALL );
ini_set( 'log_errors', 1 );

// Dateien einbinden
require_once __DIR__ . '/adminpanel/vendor/autoload.php';
include_once __DIR__ . '/lib/debug-functions.php';
include_once __DIR__ . '/lib/dbconnect.php';
include_once __DIR__ . '/lib/dbconfig.php';
$gbFunctions = include_once __DIR__ . '/lib/gb-functions.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();
$app->register( new Silex\Provider\UrlGeneratorServiceProvider() );
$app->register( new Silex\Provider\SessionServiceProvider() );

$data = array(
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'textinput' => ''
);

$app->get( '/', function () use ( $app, $twig, $db )
{
    include_once __DIR__ . '/lib/pagination.php';
    include_once __DIR__ . '/inc/processing_pagination.php';

    // Header, Content (Posts) und Footer ausgeben
    return new Response( include_once __DIR__ . '/inc/main.php', 201 );
} );

$app->post( '/', function ( Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $twig, $db, $data, $gbFunctions )
{
    $postdata = array(
        'firstname' => $firstname->get( 'firstname' ),
        'lastname' => $lastname->get( 'lastname' ),
        'email' => $email->get( 'email' ),
        'textinput' => $textinput->get( 'textinput' )
    );

    // Prüfen ob Formulardaten vorhanden wenn nicht dann raus, sonst weiter
    if ( !( isset( $postdata["firstname"] ) && isset( $postdata["lastname"] ) && isset( $postdata["email"] ) && isset( $postdata["textinput"] ) ) )
    {
        return;
    }

    include_once __DIR__ . '/inc/processing_add.php';
    include_once __DIR__ . '/lib/pagination.php';
    include_once __DIR__ . '/inc/processing_pagination.php';

    return new Response( include_once __DIR__ . '/inc/main.php', 201 );
} );

$app->run();
