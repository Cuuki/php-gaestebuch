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
        'headline' => 'Benutzer bearbeiten:',
        'submitvalue' => 'Ändern'
            ) );

    return new Response( $render, 404 );
}
else
{
    if ( updateUser( $db, $postdata, $id ) )
    {
        $render = $app['twig']->render( 'user_update_id.twig', array(
            'message' => 'Die Daten wurden geändert!',
            'headline' => 'Benutzer bearbeiten:',
            'submitvalue' => 'Ändern'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_id.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden.',
            'headline' => 'Benutzer bearbeiten:',
            'submitvalue' => 'Ändern'
                ) );

        return new Response( $render, 404 );
    }
}
