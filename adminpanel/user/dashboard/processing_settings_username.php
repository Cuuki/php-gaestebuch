<?php

use Symfony\Component\HttpFoundation\Response;

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