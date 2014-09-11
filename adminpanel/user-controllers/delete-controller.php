<?php

// Benutzer löschen
$app->get('/', function () use ( $app, $db, $apFunctions )
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

$app->get('/{id}', function( $id ) use ( $app, $db, $apFunctions )
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