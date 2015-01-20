<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'password' => $password->get( 'password' )
);

$userData = getUser( $app['db'], $id );

$id = $userData['id'];

$postdata = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );

    $render = $app['twig']->render( 'user_update_form.twig', array(
        'label_for' => 'password',
        'label_text' => 'Neues Passwort',
        'id' => $id,
        'input_name' => 'password',
        'errormessages' => $errorMessages
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updatePassword( $app['db'], $postdata['password'], $id ) )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Das Passwort wurde geändert.',
            'message_type' => 'successmessage',
            'label_for' => 'password',
            'label_text' => 'Neues Passwort',
            'id' => $id,
            'input_name' => 'password'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage',
            'label_for' => 'password',
            'label_text' => 'Neues Passwort',
            'id' => $id,
            'input_name' => 'password'
                ) );

        return new Response( $render, 404 );
    }
}
