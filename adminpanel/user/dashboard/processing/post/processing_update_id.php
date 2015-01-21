<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'username' => $username->get( 'username' ),
    'useremail' => $useremail->get( 'useremail' ),
    'password' => $password->get( 'password' )
);

include_once USER_DIR . '/dashboard/update.php';

$userData = getUser( $app['db'], $id );

$id = $userData['id'];

$postdata = $this->sanitizeLogindata( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );

    $render = $app['twig']->render( 'user_update_id.twig', array(
        'errormessages' => $errorMessages,
        'postdata' => $postdata,
        'is_active_usermanagement' => true,
        'headline' => 'Alle Daten bearbeiten:',
        'submitvalue' => 'Ändern'
            ) );

    return new Response( $render, 404 );
}
else
{
    $userData = getAllUsers( $app['db'] );

    foreach ( $userData as $user )
    {
        $username = $user['username'];
        $useremail = $user['useremail'];
    }

    if ( $postdata['username'] == $username || $postdata['useremail'] == $useremail )
    {
        $render = $app['twig']->render( 'user_update_id.twig', array(
            'message' => 'Der Benutzer existiert bereits.',
            'message_type' => 'failuremessage',
            'is_active_usermanagement' => true,
            'headline' => 'Alle Daten bearbeiten:',
            'submitvalue' => 'Anlegen'
                ) );

        return new Response( $render, 404 );
    }
    elseif ( updateUser( $app['db'], $postdata, $id ) )
    {
        $render = $app['twig']->render( 'user_update_id.twig', array(
            'message' => 'Die Daten wurden geändert!',
            'message_type' => 'successmessage',
            'is_active_usermanagement' => true,
            'headline' => 'Alle Daten bearbeiten:',
            'submitvalue' => 'Ändern'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_id.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden.',
            'message_type' => 'failuremessage',
            'is_active_usermanagement' => true,
            'headline' => 'Alle Daten bearbeiten:',
            'submitvalue' => 'Ändern'
                ) );

        return new Response( $render, 404 );
    }
}
