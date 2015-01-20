<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'oldpassword' => $password->get( 'oldpassword' ),
    'password' => $password->get( 'password' )
);

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$id = $users['id'];
$password = $users['password'];

// Wenn altes Passwort nicht mit dem aus der Session übereinstimmt
if ( password_verify( $postdata['oldpassword'], $password ) == FALSE )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldpassword',
        'oldinput_text' => 'Altes Passwort',
        'oldinput_name' => 'oldpassword',
        'newinput_for' => 'password',
        'newinput_text' => 'Neues Passwort',
        'newinput_name' => 'password',
        'message' => 'Das alte Passwort stimmt nicht mit Ihrem überein.',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
elseif ( $postdata['oldpassword'] == $postdata['password'] )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldpassword',
        'oldinput_text' => 'Altes Passwort',
        'oldinput_name' => 'oldpassword',
        'newinput_for' => 'password',
        'newinput_text' => 'Neues Passwort',
        'newinput_name' => 'password',
        'message' => 'Das alte darf nicht mit dem neuen Passwort übereinstimmen!',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updatePassword( $app['db'], $postdata['password'], $id ) )
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldpassword',
            'oldinput_text' => 'Altes Passwort',
            'oldinput_name' => 'oldpassword',
            'newinput_for' => 'password',
            'newinput_text' => 'Neues Passwort',
            'newinput_name' => 'password',
            'message' => 'Das Passwort wurde geändert!',
            'message_type' => 'successmessage'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldpassword',
            'oldinput_text' => 'Altes Passwort',
            'oldinput_name' => 'oldpassword',
            'newinput_for' => 'password',
            'newinput_text' => 'Neues Passwort',
            'newinput_name' => 'password',
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage'
                ) );

        return new Response( $render, 404 );
    }
}