<?php

error_reporting(-1);
ini_set('log_errors', 1);

// Dateien einbinden
require_once __DIR__ . '/vendor/autoload.php';
include_once __DIR__ . '/../lib/debug-functions.php';
include_once __DIR__ . '/../lib/dbconnect.php';
include_once __DIR__ . '/../lib/dbconfig.php';
$apFunctions = include_once __DIR__ . '/../lib/ap-functions.php';
$gbFunctions = include_once __DIR__ . '/../lib/gb-functions.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

define( 'ROUTES_DIR' , realpath( __DIR__ . '/routes' ));
define( 'USER_DIR' , realpath( __DIR__ . '/user' ));

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = true;

$app->get('/', function() use ($app)
{
	return $app->redirect('user/dashboard/');
});

$app->get('/auth/login', function () use ( $app )
{
	// Wenn Session nicht null dann weiterleiten auf dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}

	$route = include_once ROUTES_DIR . '/auth/login.php';

	var_dump($app['session']->get('user'));

	return $route( $app );
})->bind('login');

$app->post('/auth/login', function (Request $username, Request $password) use ($app, $db, $apFunctions)
{
	$user = array(
		'username' => $username->get('username'),
		'password' => $password->get('password')		
	);

	// Daten aus Datenbank holen
	$logindata = getLogindata( $db, $user['username'] );

	foreach ( $logindata as $data )
	{
		$hash = $data['password'];
	}

	// mit Eingabe vergleichen, Authentifizierung
	if( password_verify( $user['password'], $hash ) )
	{
		// Sessionnamen auf Usernamen setzen
		$app['session']->set('user', $user['username']);

		// Wenn eingeloggt weiterleiten auf Dashboard
		return new Response('Erfolgreich eingeloggt!
			<a href="'.$app['url_generator']->generate('dashboard').'">Weiter zur Übersicht</a>', 201);
	}
	else
	{
		return new Response('Login fehlgeschlagen.
			<a href="'.$app['url_generator']->generate('login').'">Zurück zum Loginformular</a>', 404);
	}
});

// nach eingeloggen weiterleiten auf dashboard
$app->get('/user/dashboard/', function() use ($app)
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard.php';

	return $route( $app );
})->bind('dashboard');

// Benutzer hinzufügen
$app->get('/user/dashboard/add', function () use ($app, $gbFunctions)
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/add.php';

	return $route( $app );
});

$app->post('/user/dashboard/add', function (Request $username, Request $useremail, Request $password) use ($db, $apFunctions, $gbFunctions)
{
	$user = array(
		'username' => $username->get('username'),
		'useremail' => $useremail->get('useremail'),
		'password' => $password->get('password')		
	);

	$user = sanitizeLogindata( $user );

	$invalidInput = validateForm( $user );

	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		$message = new Response( implode('<br>', $errorMessages) );
	}
	else
	{
		if( saveLogindata( $user, $db ) != 0 )
		{
			$message = new Response('Der Benutzer wurde hinzugefügt.', 201);
		}
		else
		{
			$message = new Response('Der Benutzer konnte nicht gepseichert werden!', 404);
		}
	}
	
	return $message;
});

// Benutzerdaten bearbeiten
$app->get('/user/dashboard/update', function () use ($app)
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/update.php';

	return $route( $app );
});

// Benutzer löschen
$app->get('/user/dashboard/delete', function () use ($app)
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/delete.php';

	return $route( $app );
});

// Ausloggen
$app->get('/user/dashboard/logout', function () use ($app)
{
	session_destroy();

	// nach ausloggen weiterleiten auf loginseite
	return $app->redirect($app['url_generator']->generate('login'));
});

// Loginsession starten
$app['session']->start();
$app->run();



