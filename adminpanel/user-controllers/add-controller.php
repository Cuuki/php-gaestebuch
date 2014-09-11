<?php

// Benutzer hinzufügen
$app->get('/', function () use ( $app, $gbFunctions )
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

$app->post('/', function ( Request $username, Request $useremail, Request $password ) use ( $app, $db, $apFunctions, $gbFunctions )
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