<?php

use Symfony\Component\HttpFoundation\Response;

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