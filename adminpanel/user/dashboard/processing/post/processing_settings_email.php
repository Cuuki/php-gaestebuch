<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'oldemail' => $email->get( 'oldemail' ),
    'email' => $email->get( 'email' )
);

$users = getLogindata( $db, $app['session']->get( 'user' ) );

foreach ( $users as $user )
{
    $id = $user['id'];
    $email = $user['useremail'];
}

// Wenn alter Benutzername nicht mit dem aus der Session übereinstimmt
if ( $postdata['oldemail'] != $email )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'headline' => 'E-Mail ändern:',
        'oldinput_for' => 'oldemail',
        'oldinput_text' => 'Alte E-Mail Adresse:',
        'oldinput_name' => 'oldemail',
        'newinput_for' => 'email',
        'newinput_text' => 'Neue E-Mail Adresse:',
        'newinput_name' => 'email',
        'message' => 'Die alte E-Mail Adresse stimmt nicht mit Ihrer überein.'
            ) );

    return new Response( $render, 404 );
}
elseif ( $postdata['oldemail'] == $postdata['email'] )
{
    $render = $app['twig']->render( 'settings_update_form.twig', array(
        'headline' => 'E-Mail ändern:',
        'oldinput_for' => 'oldemail',
        'oldinput_text' => 'Alte E-Mail Adresse:',
        'oldinput_name' => 'oldemail',
        'newinput_for' => 'email',
        'newinput_text' => 'Neue E-Mail Adresse:',
        'newinput_name' => 'email',
        'message' => 'Die alte darf nicht mit der neuen Adresse übereinstimmen!'
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updateEmail( $db, $postdata['email'], $id ) )
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'headline' => 'E-Mail ändern:',
            'oldinput_for' => 'oldemail',
            'oldinput_text' => 'Alte E-Mail Adresse:',
            'oldinput_name' => 'oldemail',
            'newinput_for' => 'email',
            'newinput_text' => 'Neue E-Mail Adresse:',
            'newinput_name' => 'email',
            'message' => 'Die E-Mail Adresse wurde geändert!'
                ) );

        return new Response( $render, 201 );
    }
    else
    {
        $render = $app['twig']->render( 'settings_update_form.twig', array(
            'headline' => 'E-Mail ändern:',
            'oldinput_for' => 'oldemail',
            'oldinput_text' => 'Alte E-Mail Adresse:',
            'oldinput_name' => 'oldemail',
            'newinput_for' => 'email',
            'newinput_text' => 'Neue E-Mail Adresse:',
            'newinput_name' => 'email',
            'message' => 'Die Daten konnten nicht geändert werden!'
                ) );

        return new Response( $render, 404 );
    }
}