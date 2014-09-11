<?php

// Benutzerdaten bearbeiten
$app->get('/', function () use ( $app, $db, $apFunctions )
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

$app->get('/{id}', function ( $id ) use ( $app, $db )
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

$app->post('/{id}', function ( $id, Request $username, Request $useremail, Request $password ) use ( $db, $app, $apFunctions, $gbFunctions )
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