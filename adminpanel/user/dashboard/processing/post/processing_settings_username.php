<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'oldusername' => $username->get( 'oldusername' ),
    'username' => $username->get( 'username' )
);

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$id = $users['id'];

// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
if ( $postdata['oldusername'] != $app['session']->get( 'user' ) )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldusername',
        'oldinput_text' => 'Alter Benutzername',
        'oldinput_name' => 'oldusername',
        'newinput_for' => 'username',
        'newinput_text' => 'Neuer Benutzername',
        'newinput_name' => 'username',
        'message' => 'Der alte Benutzername stimmt nicht mit Ihrem überein.',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
elseif ( $postdata['oldusername'] == $postdata['username'] )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldusername',
        'oldinput_text' => 'Alter Benutzername',
        'oldinput_name' => 'oldusername',
        'newinput_for' => 'username',
        'newinput_text' => 'Neuer Benutzername',
        'newinput_name' => 'username',
        'message' => 'Der alte darf nicht mit dem neuen Benutzernamen übereinstimmen!',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    // Ändere alten Benutzernamen wenn Funktion updateUsername() 'true' zurückgibt
    if ( updateUsername( $app['db'], $postdata['username'], $id ) )
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldusername',
            'oldinput_text' => 'Alter Benutzername',
            'oldinput_name' => 'oldusername',
            'newinput_for' => 'username',
            'newinput_text' => 'Neuer Benutzername',
            'newinput_name' => 'username',
            'message' => 'Der Benutzername wurde geändert!',
            'message_type' => 'successmessage'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldusername',
            'oldinput_text' => 'Alter Benutzername',
            'oldinput_name' => 'oldusername',
            'newinput_for' => 'username',
            'newinput_text' => 'Neuer Benutzername',
            'newinput_name' => 'username',
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage'
                ) );

        return new Response( $render, 404 );
    }
}