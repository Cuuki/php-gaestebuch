<?php

use Symfony\Component\HttpFoundation\Response;

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