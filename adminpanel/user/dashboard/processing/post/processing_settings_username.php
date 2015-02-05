<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'oldusername' => $username->get( 'oldusername' ),
    'username' => $username->get( 'username' )
);

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );
$allUsers = getAllUsers( $app['db'] );

foreach ( $allUsers as $user )
{
    $usernames[] = $user['username'];
}

// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
if ( $postdata['oldusername'] != $app['session']->get( 'user' ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername',
                'newinput_name' => 'username',
                'message' => 'Der alte Benutzername stimmt nicht mit Ihrem überein.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( $postdata['oldusername'] == $postdata['username'] )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername',
                'newinput_name' => 'username',
                'message' => 'Der alte darf nicht mit dem neuen Benutzernamen übereinstimmen!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername',
                'newinput_name' => 'username',
                'errormessages' => getErrorMessages( $invalidInput ),
                'message' => 'Sie haben keinen validen Benutzernamen angegeben!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( in_array( $postdata['username'], $usernames, true ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldusername',
                'oldinput_text' => 'Alter Benutzername',
                'oldinput_name' => 'oldusername',
                'newinput_for' => 'username',
                'newinput_text' => 'Neuer Benutzername',
                'newinput_name' => 'username',
                'message' => 'Es existiert bereits ein Benutzer mit diesem Benutzernamen!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
else
{
    // Ändere alten Benutzernamen wenn Funktion updateUsername() 'true' zurückgibt
    if ( updateUsername( $app['db'], $postdata['username'], $users['id'] ) )
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldusername',
                    'oldinput_text' => 'Alter Benutzername',
                    'oldinput_name' => 'oldusername',
                    'newinput_for' => 'username',
                    'newinput_text' => 'Neuer Benutzername',
                    'newinput_name' => 'username',
                    'message' => 'Der Benutzername wurde geändert!',
                    'relog' => true,
                    'message_type' => 'alert alert-dismissable alert-success'
                ) ), 201 );
    }
    else
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldusername',
                    'oldinput_text' => 'Alter Benutzername',
                    'oldinput_name' => 'oldusername',
                    'newinput_for' => 'username',
                    'newinput_text' => 'Neuer Benutzername',
                    'newinput_name' => 'username',
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger'
                ) ), 404 );
    }
}