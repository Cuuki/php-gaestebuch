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
// $insert
include_once __DIR__ . '/sudo-config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

define( 'ROUTES_DIR' , realpath( __DIR__ . '/routes' ));
define( 'USER_DIR' , realpath( __DIR__ . '/user' ));

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = TRUE;

$app->get('/', function () use ( $app )
{
	return $app->redirect( 'user/dashboard/' );
});

$app->get('/user/', function () use ( $app )
{
	return $app->redirect('dashboard/');
});

$app->get('/auth/login', function () use ( $app, $db, $apFunctions, $insert )
{
	// lege superuser an, wenn nicht schon vorhanden
	if( getLogindata( $db, 'sudo' )[0] == NULL )
	{
		$db->query($insert);
	}

	// Wenn Session nicht null dann weiterleiten auf dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}

	$route = include_once ROUTES_DIR . '/auth/login.php';

	return $route( $app );
})->bind('login');

$app->post('/auth/login', function ( Request $username, Request $password ) use ( $app, $db, $apFunctions )
{
	$postdata = array(
		'username' => $username->get('username'),
		'password' => $password->get('password')		
	);

	// Daten aus Datenbank holen
	$logindata = getLogindata( $db, $postdata['username'] );

	foreach ( $logindata as $data )
	{
		$hash = $data['password'];
	}

	// mit Eingabe vergleichen, Authentifizierung
	if( password_verify( $postdata['password'], $hash ) )
	{
		// Sessionnamen auf Usernamen setzen
		$app['session']->set('user', $postdata['username']);

		// Wenn eingeloggt weiterleiten auf Dashboard
		return $app->redirect($app['url_generator']->generate('dashboard'));
	}
	else
	{
		return new Response( 'Login fehlgeschlagen.' .  '</br>' . 
			'<a href="' . $app['url_generator']->generate('login') . '">Zurück zum Loginformular</a>', 404 );
	}
});


// nach eingeloggen weiterleiten auf dashboard
$app->get('/user/dashboard/', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Userzeile anzeigen als Rückmeldung das er eingeloggt ist
	$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

	$route = include_once USER_DIR . '/dashboard.php';

	return $userHeader . $route( $app );
})->bind('dashboard');


// User Einstellungen
$app->get('/user/dashboard/settings', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/settings.php';;

	return $route( $app );
})->bind('settings');

$app->get('/user/dashboard/update/username', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Alter Benutzername, Neuer Benutzername einbinden
	$route = include_once USER_DIR . '/dashboard/update-username.php';

	return $route( $app );
})->bind('changeUsername');

$app->post('/user/dashboard/update/username', function ( Request $username ) use ( $db, $app, $apFunctions )
{
	$postdata = array(
		'oldusername' => $username->get('oldusername'),
		'username' => $username->get('username')
	);

	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$id = $user['id'];
	}

	// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
	if( $postdata['oldusername'] != $app['session']->get('user') )
	{
		return new Response( 'Der alte Benutzername stimmt nicht mit Ihrem überein. 
			<a href="'.$app['url_generator']->generate('changeUsername').'">Zurück</a>', 404 );
	}
	elseif( $postdata['oldusername'] == $postdata['username'] )
	{
		return new Response( 'Der alte darf nicht mit dem neuen Benutzernamen übereinstimmen! 
			<a href="'.$app['url_generator']->generate('changeEmail').'">Zurück</a>', 404 );		
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';

		// Ändere alten Benutzernamen wenn Funktion updateUsername() 'true' zurückgibt
		if( updateUsername( $db, $postdata['username'], $id ) )
		{
			return new Response( 'Der Benutzername wurde geändert! ' . 
				'<a href="'.$app['url_generator']->generate('logout').'">Mit neuen Daten erneut einloggen</a>', 201 );
		}
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

$app->get('/user/dashboard/update/password', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Alter Benutzername, Neuer Benutzername einbinden
	$route = include_once USER_DIR . '/dashboard/update-password.php';

	return $route( $app );
})->bind('changePassword');

$app->post('/user/dashboard/update/password', function ( Request $password ) use ( $db, $app, $apFunctions )
{
	$postdata = array(
		'oldpassword' => $password->get('oldpassword'),
		'password' => $password->get('password')
	);

	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$id = $user['id'];
		$password = $user['password'];
	}

	// Wenn altes Passwort nicht mit dem aus der Session übereinstimmt
	if( password_verify( $postdata['oldpassword'], $password ) == FALSE )
	{
		return new Response( 'Das alte Passwort stimmt nicht mit Ihrem überein. 
			<a href="'.$app['url_generator']->generate('changePassword').'">Zurück</a>', 404 );
	}
	elseif( $postdata['oldpassword'] == $postdata['password'] )
	{
		return new Response( 'Das alte darf nicht mit dem neuen Passwort übereinstimmen! 
			<a href="'.$app['url_generator']->generate('changeEmail').'">Zurück</a>', 404 );		
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';
		if( updatePassword( $db, $postdata['password'], $id ) )
		{
			return new Response( 'Das Passwort wurde geändert! ' . 
				'<a href="'.$app['url_generator']->generate('logout').'">Mit neuen Daten erneut einloggen</a>', 201 );
		}
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

$app->get('/user/dashboard/update/email', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Alter Benutzername, Neuer Benutzername einbinden
	$route = include_once USER_DIR . '/dashboard/update-email.php';

	return $route( $app );
})->bind('changeEmail');

$app->post('/user/dashboard/update/email', function ( Request $email ) use ( $db, $app, $apFunctions )
{
	$postdata = array(
		'oldemail' => $email->get('oldemail'),
		'email' => $email->get('email')
	);

	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{	
		$id = $user['id'];
		$email = $user['useremail'];
	}

	// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
	if( $postdata['oldemail'] != $email )
	{
		return new Response( 'Die alte E-Mail Adresse stimmt nicht mit Ihrer überein. 
			<a href="'.$app['url_generator']->generate('changeEmail').'">Zurück</a>', 404 );
	}
	elseif( $postdata['oldemail'] == $postdata['email'] )
	{
		return new Response( 'Die alte darf nicht mit der neuen Adresse übereinstimmen! 
			<a href="'.$app['url_generator']->generate('changeEmail').'">Zurück</a>', 404 );		
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';

		if( updateEmail( $db, $postdata['email'], $id ) )
		{
			return new Response( 'Die E-Mail Adresse wurde geändert! ' . 
				'<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
		}
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});


// Benutzer hinzufügen
$app->get('/user/dashboard/add', function () use ( $app, $gbFunctions )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

	$route = include_once USER_DIR . '/dashboard/add.php';

	return $userHeader . $route( $app );
})->bind('add');

$app->post('/user/dashboard/add', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'username' => $username->get('username'),
		'useremail' => $useremail->get('useremail'),
		'password' => $password->get('password')		
	);

	$postdata = sanitizeLogindata( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		$message = new Response( implode('<br>', $errorMessages) . 
			'<br>' . '<a href="'.$app['url_generator']->generate('add').'">Zurück</a>', 404 );
	}
	else
	{
		if( saveLogindata( $postdata, $db ) != 0 )
		{
			$message = new Response( 'Der Benutzer wurde hinzugefügt. 
				<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
		}
		else
		{
			$message = new Response( 'Der Benutzer konnte nicht gepseichert werden! 
				<a href="'.$app['url_generator']->generate('add').'">Zurück</a>', 404 );
		}
	}
	
	return $message;
});


// Benutzerdaten bearbeiten
$app->get('/user/dashboard/update/', function () use ( $app, $db, $apFunctions )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

	$getUsers = getUsers( $db );

	include_once USER_DIR . '/dashboard/update.php';
	$displayUsers = displayUsers( $getUsers );

	return new Response( $userHeader . $displayUsers . 
		'<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('update');

$app->get('/user/dashboard/update/{id}', function ( $id ) use ( $app, $db )
{
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

	include_once USER_DIR . '/dashboard/update.php';
	// Ausgewählten Benutzer aus Datenbank holen mit $id aus URL
	$selectedUser = getSelectedUser( $db, $id );
	$displaySelectedUser = displaySelectedUser( $selectedUser );
	$getForm = getForm();

	return new Response( $userHeader . $displaySelectedUser . $getForm, 201 );
});

$app->post('/user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'username' => $username->get('username'),
		'useremail' => $useremail->get('useremail'),
		'password' => $password->get('password')
	);

	include_once USER_DIR . '/dashboard/update.php';

	$selectedUser = getSelectedUser( $db, $id );

	foreach($selectedUser as $user)
	{
		$id = $user['id'];
		$oldusername = $user['username'];
		$oldemail = $user['useremail'];
		$oldpassword = $user['password'];
	}

	$postdata = sanitizeLogindata( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages) . 
				'<br>' . '<a href="'.$app['url_generator']->generate('update').'">Zurück</a>', 201 );
	}
	elseif( $postdata['username'] == $oldusername || $postdata['useremail'] == $oldemail ||  $postdata['password'] == $oldpassword )
	{
		return new Response( 'Die alten Daten dürfen nicht mit den neuen übereinstimmen! 
			<a href="'.$app['url_generator']->generate('update').'">Zurück</a>', 404 );		
	}
	else
	{
		if( update( $db, $postdata, $id ) )
		{
			return new Response( 'Die Daten wurden geändert! ' . 
				'<a href="'.$app['url_generator']->generate('update').'">Zurück</a>', 201 );
		}
		// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});


// Benutzer löschen
$app->get('/user/dashboard/delete/', function () use ( $app, $db, $apFunctions )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

	$getUsers = getUsers( $db );

	include_once USER_DIR . '/dashboard/delete.php';
	$displayUsers = displayUsers( $getUsers );

	return new Response( $userHeader . $displayUsers . 
		'<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('delete');

$app->get('/user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
{
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Daten für gerade eingeloggten User aus Datenbank holen
	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$role = $user['role'];	
	}

	// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer nichts löschen
	if( $role == 'adm' )
	{
		return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen.
			<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
	}
	else
	{
		include_once USER_DIR . '/dashboard/delete.php';
		deleteUser( $db, $id );

		return new Response( 'User erfolgreich gelöscht!
			<a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 201 );
	}
});


// Ausloggen
$app->get('/user/dashboard/logout', function () use ( $app )
{
	session_destroy();

	// nach ausloggen weiterleiten auf loginseite
	return $app->redirect($app['url_generator']->generate('login'));
})->bind('logout');


// Loginsession starten
$app['session']->start();

$app->run();



