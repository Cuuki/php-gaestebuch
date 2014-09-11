<?php

// User Einstellungen
$app->get('/', function () use ( $app )
{
	// Wenn Session null weiterleiten auf login
	if( ($app['session']->get('user')) == NULL )
	{
		return $app->redirect($app['url_generator']->generate('login'));	
	}

	$route = include_once USER_DIR . '/settings.php';;

	return $route( $app );
})->bind('settings');

$app->get('/username', function () use ( $app )
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

$app->post('/username', function ( Request $username ) use ( $db, $app, $apFunctions )
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

$app->get('/password', function () use ( $app )
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

$app->post('/password', function ( Request $password ) use ( $db, $app, $apFunctions )
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

$app->get('/email', function () use ( $app )
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

$app->post('/email', function ( Request $email ) use ( $db, $app, $apFunctions )
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