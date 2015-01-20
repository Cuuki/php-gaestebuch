<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'oldemail' => $email->get( 'oldemail' ),
    'email' => $email->get( 'email' )
);

$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$id = $users['id'];
$email = $users['useremail'];

// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
if ( $postdata['oldemail'] != $email )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldemail',
        'oldinput_text' => 'Alte E-Mail Adresse',
        'oldinput_name' => 'oldemail',
        'newinput_for' => 'email',
        'newinput_text' => 'Neue E-Mail Adresse',
        'newinput_name' => 'email',
        'message' => 'Die alte E-Mail Adresse stimmt nicht mit Ihrer überein.',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
elseif ( $postdata['oldemail'] == $postdata['email'] )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'oldinput_for' => 'oldemail',
        'oldinput_text' => 'Alte E-Mail Adresse',
        'oldinput_name' => 'oldemail',
        'newinput_for' => 'email',
        'newinput_text' => 'Neue E-Mail Adresse',
        'newinput_name' => 'email',
        'message' => 'Die alte darf nicht mit der neuen Adresse übereinstimmen!',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updateEmail( $app['db'], $postdata['email'], $id ) )
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldemail',
            'oldinput_text' => 'Alte E-Mail Adresse',
            'oldinput_name' => 'oldemail',
            'newinput_for' => 'email',
            'newinput_text' => 'Neue E-Mail Adresse',
            'newinput_name' => 'email',
            'message' => 'Die E-Mail Adresse wurde geändert!',
            'message_type' => 'successmessage'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'oldinput_for' => 'oldemail',
            'oldinput_text' => 'Alte E-Mail Adresse',
            'oldinput_name' => 'oldemail',
            'newinput_for' => 'email',
            'newinput_text' => 'Neue E-Mail Adresse',
            'newinput_name' => 'email',
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage'
                ) );

        return new Response( $render, 404 );
    }
}