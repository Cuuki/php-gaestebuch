<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'oldpassword' => $password->get( 'oldpassword' ),
    'password' => $password->get( 'password' )
);

$users = getLogindata( $db, $app['session']->get( 'user' ) );

foreach ( $users as $user )
{
    $id = $user['id'];
    $password = $user['password'];
}

// Wenn altes Passwort nicht mit dem aus der Session übereinstimmt
if ( password_verify( $postdata['oldpassword'], $password ) == FALSE )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'headline' => 'Passwort ändern:',
        'oldinput_for' => 'oldpassword',
        'oldinput_text' => 'Altes Passwort:',
        'oldinput_name' => 'oldpassword',
        'newinput_for' => 'password',
        'newinput_text' => 'Neues Passwort:',
        'newinput_name' => 'password',
        'message' => 'Das alte Passwort stimmt nicht mit Ihrem überein.'
            ) );

    return new Response( $render, 404 );
}
elseif ( $postdata['oldpassword'] == $postdata['password'] )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'headline' => 'Passwort ändern:',
        'oldinput_for' => 'oldpassword',
        'oldinput_text' => 'Altes Passwort:',
        'oldinput_name' => 'oldpassword',
        'newinput_for' => 'password',
        'newinput_text' => 'Neues Passwort:',
        'newinput_name' => 'password',
        'message' => 'Das alte darf nicht mit dem neuen Passwort übereinstimmen!'
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updatePassword( $db, $postdata['password'], $id ) )
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'headline' => 'Passwort ändern:',
            'oldinput_for' => 'oldpassword',
            'oldinput_text' => 'Altes Passwort:',
            'oldinput_name' => 'oldpassword',
            'newinput_for' => 'password',
            'newinput_text' => 'Neues Passwort:',
            'newinput_name' => 'password',
            'message' => 'Das Passwort wurde geändert!'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'headline' => 'Passwort ändern:',
            'oldinput_for' => 'oldpassword',
            'oldinput_text' => 'Altes Passwort:',
            'oldinput_name' => 'oldpassword',
            'newinput_for' => 'password',
            'newinput_text' => 'Neues Passwort:',
            'newinput_name' => 'password',
            'message' => 'Die Daten konnten nicht geändert werden!'
                ) );

        return new Response( $render, 404 );
    }
}