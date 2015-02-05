<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'oldpassword' => $password->get( 'oldpassword' ),
    'password' => $password->get( 'password' )
);

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

// Wenn altes Passwort nicht mit dem aus der Session übereinstimmt
if ( password_verify( $postdata['oldpassword'], $users['password'] ) == FALSE )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldpassword',
                'oldinput_text' => 'Altes Passwort',
                'oldinput_name' => 'oldpassword',
                'newinput_for' => 'password',
                'newinput_text' => 'Neues Passwort',
                'newinput_name' => 'password',
                'message' => 'Das alte Passwort stimmt nicht mit Ihrem überein.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( $postdata['oldpassword'] == $postdata['password'] )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldpassword',
                'oldinput_text' => 'Altes Passwort',
                'oldinput_name' => 'oldpassword',
                'newinput_for' => 'password',
                'newinput_text' => 'Neues Passwort',
                'newinput_name' => 'password',
                'message' => 'Das alte darf nicht mit dem neuen Passwort übereinstimmen!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                'is_active_settings' => true,
                'oldinput_for' => 'oldpassword',
                'oldinput_text' => 'Altes Passwort',
                'oldinput_name' => 'oldpassword',
                'newinput_for' => 'password',
                'newinput_text' => 'Neues Passwort',
                'newinput_name' => 'password',
                'errormessages' => getErrorMessages( $invalidInput ),
                'message' => 'Sie haben kein valides Passwort angegeben!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
else
{
    if ( updatePassword( $app['db'], $postdata['password'], $users['id'] ) )
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldpassword',
                    'oldinput_text' => 'Altes Passwort',
                    'oldinput_name' => 'oldpassword',
                    'newinput_for' => 'password',
                    'newinput_text' => 'Neues Passwort',
                    'newinput_name' => 'password',
                    'message' => 'Das Passwort wurde geändert!',
                    'relog' => true,
                    'message_type' => 'alert alert-dismissable alert-success'
                ) ), 201 );
    }
    else
    {
        return new Response( $app['twig']->render( 'settings_update_form.twig', array(
                    'is_active_settings' => true,
                    'oldinput_for' => 'oldpassword',
                    'oldinput_text' => 'Altes Passwort',
                    'oldinput_name' => 'oldpassword',
                    'newinput_for' => 'password',
                    'newinput_text' => 'Neues Passwort',
                    'newinput_name' => 'password',
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger'
                ) ), 404 );
    }
}