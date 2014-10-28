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
// Superuser anlegen
include_once __DIR__ . '/sudo-config.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;

define( 'ROUTES_DIR' , realpath( __DIR__ . '/routes' ));
define( 'USER_DIR' , realpath( __DIR__ . '/user' ));
define( 'POST_DIR' , realpath( __DIR__ . '/post' ));

// Mail Encoding auf UTF-8 setzen
mb_internal_encoding("UTF-8");

$app = new Silex\Application();
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = TRUE;

$app['session.storage.options'] = array(
	'lifetime' => 900
);

$app->get('/', function () use ( $app )
{
	return $app->redirect( 'user/dashboard/' );
});

$app->get('/post/', function () use ( $app )
{
	return $app->redirect( '../user/dashboard/' );
});

$app->get('/user/', function () use ( $app )
{
	return $app->redirect('dashboard/');
});

$app->get('/auth/', function () use ( $app )
{
	return $app->redirect( 'login' );
});

$app->get('/auth/login', function () use ( $app, $db, $apFunctions )
{
	// Wenn bereits eingeloggt weiterleiten auf Dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}

	$route = include_once ROUTES_DIR . '/auth/login.php';

	return $route( $app );
})->bind('login');

$app->post('/auth/login', function ( Request $username, Request $password, Request $staylogged ) use ( $app, $db, $apFunctions )
{
	$postdata = array(
		'username' => $username->get('username'),
		'password' => $password->get('password'),
		'staylogged' => $staylogged->get('staylogged')
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
		// Wenn 'angemeldet bleiben' ausgewählt lifetime auf 0 setzen und nicht automatisch ausloggen
		if( $postdata['staylogged'] == 'staylogged' )
		{	
			$app['session']->set('cookie_lifetime', 0);
		}
		else
		{
			// Wenn nicht Cookie Lifetime auf einen anderen wert als 0 setzen
			$app['session']->set('cookie_lifetime', 900);
		}
		// Sessionnamen auf Usernamen setzen
		$app['session']->set('user', $postdata['username']);
		$app['session']->set('time', time());

		// Weiterleiten auf Dashboard
		return $app->redirect($app['url_generator']->generate('dashboard'));
	}
	else
	{
		return new Response( 'Login fehlgeschlagen. Ihre Daten sind nicht korrekt.' .  '</br>' . 
			'<a href="' . $app['url_generator']->generate('login') . '">Zurück zum Loginformular</a>', 404 );
	}
});

$app->get('/auth/reset', function () use ( $app, $db, $apFunctions )
{
	// Wenn Session nicht null dann weiterleiten auf dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}

	include_once ROUTES_DIR . '/auth/reset.php';

	return getResetForm();
});

$app->post('/auth/reset', function ( Request $email ) use ( $db, $app )
{
	$postdata = array(
		'email' => $email->get('email')
	);

	include_once ROUTES_DIR . '/auth/reset.php';
	$result = getMail( $db, $postdata['email'] );

	// Abfrage ob E-Mail mit einer aus DB übereinstimmt
	if( $result['useremail'] == NULL )
	{
		return new Response ( 'Es existiert kein Benutzer mit der angegebenen E-Mail Adresse.
			<br><a href="../auth/reset">Zurück zur Eingabe</a>', 404 );
	}

	$code = mt_rand( 1000, 9999 );

	$subject = 'Authentifizierungscode Passwort-Neuvergabe';

	$message = 'Bitte geben Sie folgenden Code: ' . $code . ' im Eingabefeld der Website ein.' . PHP_EOL . 'ACHTUNG der Code wird aus Sicherheitsgründen entfernt, nachdem er eingegeben wurde (Laden Sie die Seite danach nicht neu, ansonsten müssen Sie erst wieder einen neuen Code anfordern)!';

	// UTF-8 codierte mail versenden
	mb_send_mail( $postdata['email'], $subject, $message );

	// Code und ID von User der diesen angefordert hat in Datenbank speichern
	include_once ROUTES_DIR . '/auth/code.php';
	saveCode( $db, $code, $result['id'] );

	return new Response ( 'Sie erhalten in Kürze eine E-Mail mit dem Authentifizierungscode. 
		<br><a href="reset/code">Weiter zur Codeeingabe</a>', 201 );
});

$app->get('auth/reset/code', function () use ( $app )
{
	// Wenn Session nicht null dann weiterleiten auf dashboard
	if( ($app['session']->get('user')) != NULL )
	{
		return $app->redirect($app['url_generator']->generate('dashboard'));	
	}

	include_once ROUTES_DIR . '/auth/code.php';

	return getCodeForm();
});

$app->post('auth/reset/code', function ( Request $code, Request $password ) use ( $db, $app )
{
	$postdata = array(
		'code' => $code->get('code'),
		'password' => $password->get('password')
	);

	include_once ROUTES_DIR . '/auth/code.php';
	$result = getCode( $db, $postdata['code'] );

	// Abfrage ob Code mit einem aus DB übereinstimmt
	if( $result['code'] == NULL )
	{
		return new Response ( 'Sie haben den falschen Code eingegegeben.
			<br><a href="../reset/code">Zurück zur Eingabe</a>', 404 );
	}
	else
	{
		// Altes Passwort mit dem neuen überschreiben wenn Code stimmt
		include_once USER_DIR . '/dashboard/update.php';
		updatePassword( $db, $postdata['password'], $result['id_user'] );

		// Wenn Code eingegeben wurde lösche ihn aus DB
		deleteCode( $db, $result['code'] );

		return new Response ( 'Ihr Passwort wurde geändert! <a href="' 
			. $app['url_generator']->generate('login') . '">Zurück zum Login</a>', 201 );
	}
});

// User Einstellungen
$app->get('/user/dashboard/settings', function () use ( $app )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/settings.php';

	return $route( $app );
})->bind('settings');

// Userzeile als Rückmeldung das er eingeloggt ist
$userHeader = '<header><h3 style="text-align: right">Sie sind als <a href="/php-gaestebuch/adminpanel' . $app['url_generator']->generate('settings') .'">' . $app['session']->get('user') . '</a> eingeloggt.</h3></header>';

$app->get('/user/dashboard/', function () use ( $app, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$loggedInSince = $app['session']->get('time');

	$route = include_once USER_DIR . '/dashboard.php';

	return new Response ( $userHeader . 'Sie sind eingeloggt seid: ' . date('h:i:s A', $loggedInSince) . '<br>' . $route( $app ), 201 );
})->bind('dashboard');

$app->get('/user/dashboard/settings/username', function () use ( $app )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Alter Benutzername, Neuer Benutzername einbinden
	$route = include_once USER_DIR . '/dashboard/settings-username.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changeUsername');

$app->post('/user/dashboard/settings/username', function ( Request $username ) use ( $db, $app, $apFunctions )
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
			<a href="'.$app['url_generator']->generate('changeUsername').'">Zurück</a>', 404 );		
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

$app->get('/user/dashboard/settings/password', function () use ( $app )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Altes Passwort, Neues Passwort einbinden
	$route = include_once USER_DIR . '/dashboard/settings-password.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changePassword');

$app->post('/user/dashboard/settings/password', function ( Request $password ) use ( $db, $app, $apFunctions )
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
			<a href="'.$app['url_generator']->generate('changePassword').'">Zurück</a>', 404 );		
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

$app->get('/user/dashboard/settings/email', function () use ( $app )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Alte E-Mail, Neue E-Mail einbinden
	$route = include_once USER_DIR . '/dashboard/settings-email.php';

	return new Response ( $route( $app ) . '<a href="'.$app['url_generator']->generate('settings').'">Zurück zum Profil</a>', 201 );
})->bind('changeEmail');

$app->post('/user/dashboard/settings/email', function ( Request $email ) use ( $db, $app, $apFunctions )
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
				'<a href="'.$app['url_generator']->generate('logout').'">Mit neuen Daten erneut einloggen</a>', 201 );
		}
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

// Benutzer hinzufügen
$app->get('/user/dashboard/add', function () use ( $app, $gbFunctions, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/add.php';

	return new Response ( $userHeader . $route( $app ), 201 );
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
		$subject = 'Neu angelegter Benutzer';
		$message = 'Hallo ' . $postdata['username'] . ',' . PHP_EOL . 'Sie wurden von ' . $app['session']->get('user') . ' als neuer Benutzer für das Adminpanel hinzugefügt. Sie können sich nun mit folgendem Passwort anmelden: ' . $postdata['password'] . ' (Sie können das Passwort jederzeit auf Ihrem Profil ändern).' . PHP_EOL . 'Mit freundlichen Grüßen' . PHP_EOL . 'Ihr Service Team';

		if( saveLogindata( $postdata, $db ) != 0 )
		{
			// Mail an angegebene E-Mail Adresse mit Logindaten
			mb_send_mail( $postdata['useremail'], $subject, $message );

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
$app->get('/user/dashboard/update/', function () use ( $app, $db, $apFunctions, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Daten für gerade eingeloggten User aus Datenbank holen
	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$role = $user['role'];	
	}

	// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
	if( $role == 'adm' )
	{
		return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.
			<br><a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
	}

	include_once __DIR__ . '/../lib/pagination.php';

	$totalentries = totalEntries( $db );

	// Anzahl an angezeigen Einträgen pro Seite
	$rowsperpage = 5;

	$totalpages = totalPages( $totalentries, $rowsperpage );

	// aktuelle Seite oder Default
	if ( isset($_GET['currentpage']) && is_numeric($_GET['currentpage']) )
	{
		$currentpage = (int) $_GET['currentpage'];
	}
	else
	{
		// Nummer von Default-Seite
		$currentpage = 1;
	}

	if ( $currentpage > $totalpages )
	{
		// Aktuelle Seite = letzte Seite
		$currentpage = $totalpages;
	}
	if ( $currentpage < 1 )
	{
		$currentpage = 1;
	}

	$getAllUsers = getAllUsers( $db );

	include_once USER_DIR . '/dashboard/update.php';
	$displayUpdateUsers = displayUpdateUsers( $getAllUsers );

	return new Response( $userHeader . displayPagination ( $currentpage, $totalpages ) . $displayUpdateUsers . '<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('update');

$app->get('/user/dashboard/update/{id}', function ( $id ) use ( $app, $db, $apFunctions, $gbFunctions, $userHeader )
{
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Daten für gerade eingeloggten User aus Datenbank holen
	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$role = $user['role'];	
	}

	// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
	if( $role == 'adm' )
	{
		return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.
			<br><a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
	}

	include_once USER_DIR . '/dashboard/update.php';
	// Ausgewählten Benutzer aus Datenbank holen mit $id aus URL
	$userData = getUser( $db, $id );
	$displayUser = displayUser( $userData );
	$userForm = getUpdateForm();

	return new Response( $userHeader . $displayUser . $userForm, 201 );
});

$app->post('/user/dashboard/update/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'username' => $username->get('username'),
		'useremail' => $useremail->get('useremail'),
		'password' => $password->get('password')
	);

	include_once USER_DIR . '/dashboard/update.php';

	$userData = getUser( $db, $id );

	foreach($userData as $user)
	{
		$id = $user['id'];
	}

	$postdata = sanitizeLogindata( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty( $invalidInput ) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages), 201 );
	}
	else
	{
		if( updateUser( $db, $postdata, $id ) )
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

$app->get('/user/dashboard/update/{id}/username', function ( $id ) use ( $app, $db, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/update-username.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/username', function ( $id, Request $username ) use ( $app, $db, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'username' => $username->get('username')
	);	

	$userData = getUser( $db, $id );

	foreach($userData as $user)
	{
		$id = $user['id'];
	}

	$postdata = sanitizeIndividualFields( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty( $invalidInput ) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages), 201 );
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';
		if( updateUsername( $db, $postdata['username'], $id ) )
		{
			return new Response( 'Die Daten wurden geändert! ' . 
				'<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
		}
		// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

$app->get('/user/dashboard/update/{id}/email', function ( $id ) use ( $app, $db, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/update-email.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/email', function ( $id, Request $useremail ) use ( $app, $db, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'useremail' => $useremail->get('useremail')
	);

	$userData = getUser( $db, $id );

	foreach($userData as $user)
	{
		$id = $user['id'];
	}

	$postdata = sanitizeIndividualFields( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty( $invalidInput ) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages), 201 );
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';
		if( updateEmail( $db, $postdata['useremail'], $id ) )
		{
			return new Response( 'Die Daten wurden geändert! ' . 
				'<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
		}
		// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

$app->get('/user/dashboard/update/{id}/password', function ( $id ) use ( $app, $db, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/dashboard/update-password.php';

	return new Response( $userHeader . $route( $app ) . '<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
});

$app->post('/user/dashboard/update/{id}/password', function ( $id, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
{
	$postdata = array(
		'password' => $password->get('password')
	);

	$userData = getUser( $db, $id );

	foreach($userData as $user)
	{
		$id = $user['id'];
	}

	$postdata = sanitizeIndividualFields( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty( $invalidInput ) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages), 201 );
	}
	else
	{
		include_once USER_DIR . '/dashboard/update.php';
		if( updatePassword( $db, $postdata['password'], $id ) )
		{
			return new Response( 'Die Daten wurden geändert! ' . 
				'<a href="'.$app['url_generator']->generate('update') . $id .'">Zurück</a>', 201 );
		}
		// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

// Benutzer löschen
$app->get('/user/dashboard/delete/', function () use ( $app, $db, $apFunctions, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Daten für gerade eingeloggten User aus Datenbank holen
	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$role = $user['role'];	
	}

	include_once USER_DIR . '/dashboard/delete.php';

	// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
	if( $role == 'adm' )
	{
		return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.
			<br><a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
	}

	include_once __DIR__ . '/../lib/pagination.php';

	$totalentries = totalEntries( $db );

	// Anzahl an angezeigen Einträgen pro Seite
	$rowsperpage = 5;

	$totalpages = totalPages( $totalentries, $rowsperpage );

	// aktuelle Seite oder Default
	if ( isset($_GET['currentpage']) && is_numeric($_GET['currentpage']) )
	{
		$currentpage = (int) $_GET['currentpage'];
	}
	else
	{
		// Nummer von Default-Seite
		$currentpage = 1;
	}

	if ( $currentpage > $totalpages )
	{
		// Aktuelle Seite = letzte Seite
		$currentpage = $totalpages;
	}
	if ( $currentpage < 1 )
	{
		$currentpage = 1;
	}

	$getAllUsers = getAllUsers( $db );

	include_once USER_DIR . '/dashboard/delete.php';
	$displayDeleteUsers = displayDeleteUsers( $getAllUsers );

	return new Response( $userHeader . displayPagination ( $currentpage, $totalpages ) . $displayDeleteUsers .
		'<a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 201 );
})->bind('delete');

$app->get('/user/dashboard/delete/{id}', function( $id ) use ( $app, $db, $apFunctions )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	// Daten für gerade eingeloggten User aus Datenbank holen
	$users = getLogindata( $db, $app['session']->get('user') );

	foreach($users as $user)
	{
		$usernameSession = $user['username'];
		$role = $user['role'];
	}

	$selectedUser = getUser( $db, $id );

	foreach ( $selectedUser as $select )
	{
		$usernameSelected = $select['username'];
	}

	include_once USER_DIR . '/dashboard/delete.php';

	// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
	if( $role == 'adm' )
	{
		return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.
			<br><a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
	}
	// User aus Session und User aus löschen stimmen überein -> nicht selbst löschen
	elseif ( $usernameSession == $usernameSelected )
	{
		return new Response( 'Sie können sich nicht selbst löschen!
					<a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 404 );		
	}
	// Wenn deleteUser 'true' zurück gibt wurde User erfolgreich gelöscht
	elseif( deleteUser( $db, $id ) )
	{
		return new Response( 'User erfolgreich gelöscht!
			<a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 201 );
	}
	else
	{
		return new Response( 'User konnte nicht gelöscht werden, versuchen sie es erneut!
			<a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 404 );		
	}
});

// Auf Home des Gästebuchs weiterleiten zur Sprungmarke 'add'
$app->get('/post/add', function () use ( $app )
{
	return $app->redirect('../../#add');
});

$app->get('/post/update', function () use ( $db, $app, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	include_once __DIR__ . '/../lib/pagination.php';

	$totalentries = totalEntries( $db );

	// Anzahl an angezeigen Einträgen pro Seite
	$rowsperpage = 5;

	$totalpages = totalPages( $totalentries, $rowsperpage );

	// aktuelle Seite oder Default
	if ( isset($_GET['currentpage']) && is_numeric($_GET['currentpage']) )
	{
		$currentpage = (int) $_GET['currentpage'];
	}
	else
	{
		// Nummer von Default-Seite
		$currentpage = 1;
	}

	if ( $currentpage > $totalpages )
	{
		// Aktuelle Seite = letzte Seite
		$currentpage = $totalpages;
	}
	if ( $currentpage < 1 )
	{
		$currentpage = 1;
	}

	$posts = getPosts( $db, $rowsperpage, $currentpage );

	include_once POST_DIR . '/update.php';

	return new Response ( $userHeader . displayPagination( $currentpage, $totalpages ) . displayUpdateEntries( $posts ) . '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
})->bind('postUpdate');

$app->get('/post/update/{id}', function ( $id ) use ( $db, $app, $gbFunctions, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	include_once POST_DIR . '/update.php';

	$entryData = getEntry( $db, $id );
	$postForm = getUpdateForm();

	return new Response ( $userHeader . displayPosts( $entryData ) . $postForm, 201 ) ;
});

$app->post('/post/update/{id}', function ( $id, Request $firstname, Request $lastname, Request $email, Request $textinput ) use ( $db, $app, $gbFunctions, $userHeader )
{
	$postdata = array(
		'firstname' => $firstname->get('firstname'),
		'lastname' => $lastname->get('lastname'),
		'email' => $email->get('email'),
		'textinput' => $textinput->get('textinput')
	);

	include_once POST_DIR . '/update.php';

	$entryData = getEntry( $db, $id );

	foreach($entryData as $post)
	{
		$oldcontent = $post['content'];
	}

	$postdata = sanitizeData( $postdata );

	$invalidInput = validateForm( $postdata );

	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
		return new Response( implode('<br>', $errorMessages), 201 );
	}
	elseif( $postdata['textinput'] == $oldcontent )
	{
		return new Response( 'Bitte geben Sie mindestenes einen neuen Beitrag ein!', 404 );		
	}
	else
	{
		if( updatePost( $db, $postdata, $id ) )
		{
			return new Response( 'Die Daten wurden geändert!  ' . 
				'<a href="'.$app['url_generator']->generate('postUpdate').'">Zurück</a>', 201 );
		}
		// Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
		else
		{
			return new Response( 'Die Daten konnten nicht geändert werden! ', 404 );		
		}
	}
});

$app->get('/post/delete', function () use ( $db, $app, $userHeader )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	include_once __DIR__ . '/../lib/pagination.php';

	$totalentries = totalEntries($db);

	$rowsperpage = 5;

	$totalpages = totalPages($totalentries, $rowsperpage);

	// aktuelle Seite oder Default
	if ( isset($_GET['currentpage']) && is_numeric($_GET['currentpage']) )
	{
		$currentpage = (int) $_GET['currentpage'];
	}
	else
	{
		// Nummer von Default-Seite
		$currentpage = 1;
	}

	if ($currentpage > $totalpages)
	{
		// Aktuelle Seite = letzter Seite
		$currentpage = $totalpages;
	}
	if ($currentpage < 1)
	{
		$currentpage = 1;
	}

	$posts = getPosts( $db, $rowsperpage, $currentpage );

	include_once POST_DIR . '/delete.php';

	return new Response ( $userHeader . displayPagination( $currentpage, $totalpages ) . displayDeleteEntries( $posts ) . '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
})->bind('deletePost');

$app->get('/post/delete/{id}', function ( $id ) use ( $db, $app )
{
	// Wenn Session NULL weiterleiten auf Login da User nicht mehr eingeloggt ist
	if( ($app['session']->get('user')) === NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	include_once POST_DIR . '/delete.php';

	if( deletePost( $db, $id ) )
	{
		return new Response( 'Beitrag erfolgreich gelöscht!
			<a href="'.$app['url_generator']->generate('deletePost').'">Zurück</a>', 201 );
	}
	else
	{
		return new Response( 'Der Beitrag konnte nicht gelöscht werden, versuchen sie es erneut!
					<a href="'.$app['url_generator']->generate('deletePost').'">Zurück</a>', 404 );
	}
});

// Ausloggen
$app->get('/user/dashboard/logout', function () use ( $app )
{
	session_destroy();

	// nach ausloggen weiterleiten auf loginseite
	return $app->redirect( $app['url_generator']->generate('login') );
})->bind('logout');

// Loginsession starten
$app['session']->start();

// Letzte Aktivität in der Session
$sessionLastUsed = $app['session']->getMetadataBag()->getLastUsed();

var_dump($app['session']->get('cookie_lifetime'));

// Wenn nach 15 Minuten (900sek) keine Aktivität in der Session war und das Cookie Lifetime nicht 0 ist zerstöre diese
if ( ( $app['session']->get('cookie_lifetime') !== 0 ) && ( time() - $sessionLastUsed > 900 ) )
{
    session_destroy();
}

$app->run();