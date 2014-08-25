<?php

error_reporting(-1);
ini_set('log_errors', 1);

// Dateien einbinden
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/../lib/debug-functions.php';
include_once __DIR__ . '/../lib/dbconnect.php';
include_once __DIR__ . '/../lib/dbconfig.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

define( 'ROUTES_DIR' , realpath( __DIR__ . '/routes' ));
define( 'USER_DIR' , realpath( __DIR__ . '/user' ));

$app = new Silex\Application();

$app['debug'] = true;

// First method will be used for match
$app->match('/auth/login', function () use ( $app )
{
	$route = include_once ROUTES_DIR . '/auth/login.php';

	return $route( $app );
});

// nach eingeloggen weiterleiten auf dashboard
$app->match('/user/dashboard/', function() use ($app)
{
	$route = include_once USER_DIR . '/dashboard.php';

	return $route( $app );
});

// Benutzer hinzufügen
$app->match('/user/dashboard/add', function () use ($app)
{
	include_once USER_DIR . '/dashboard/add.php';

	return form( $app );
});

// Benutzerdaten bearbeiten
$app->match('/user/dashboard/update', function () use ($app)
{
	$route = include_once USER_DIR . '/dashboard/update.php';

	return $route( $app );
});

// Benutzer löschen
$app->match('/user/dashboard/delete', function () use ($app)
{
	$route = include_once USER_DIR . '/dashboard/delete.php';

	return $route( $app );
});

// Ausloggem
$app->match('/user/dashboard/logout', function () use ($app)
{
	$route = include_once USER_DIR . '/dashboard/logout.php';

	return $route( $app );
})
->method('GET');

$app->post('/user/dashboard/add', function (Request $username, Request $useremail, Request $password) use ($db)
{
	include_once USER_DIR . '/dashboard/add.php';

	$user = array(
		'username' => $username->get('username'),
		'useremail' => $useremail->get('useremail'),
		'password' => $password->get('password')		
	);

	$user = sanitizeLogindata( $user );

	if( saveLogindata( $user, $db ) != 0 )
	{
		return new Response('<p>Ihr Beitrag wurde gespeichert!</p>', 201);
	}

	return new Response('<p>Ihr Beitrag konnte nicht gepseichert werden!</p>', 404);
});

// nach ausloggen weiterleiten auf loginseite
// $app->get('/user/dashboard/logout')

$app->run();



