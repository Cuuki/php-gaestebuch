<?php

use Symfony\Component\HttpFoundation\Response;

include_once ROUTES_DIR . '/auth/password_forget.php';
include_once USER_DIR . '/update.php';

$postdata = array(
    'code' => $code->get( 'code' ),
    'password' => $password->get( 'password' )
);

$result = getCode( $app['db'], $postdata['code'] );
$postdata = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $postdata );

// Abfrage ob Code mit einem aus DB übereinstimmt
if ( $result['code'] == NULL )
{
    return new Response( $app['twig']->render( 'code_form.twig', array(
                'message' => 'Sie haben den falschen Code eingegegeben.',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
elseif ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );

    return new Response( $app['twig']->render( 'code_form.twig', array(
                'message' => $errorMessages['password'],
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
// Altes Passwort mit dem neuen überschreiben wenn Code stimmt
elseif ( updatePassword( $app['db'], $postdata['password'], $result['id_user'] ) )
{
    // Wenn Code eingegeben wurde lösche ihn aus DB
    deleteCode( $app['db'], $result['code'] );

    return new Response( $app['twig']->render( 'code_form.twig', array(
                'message' => 'Ihr Passwort wurde geändert!',
                'message_type' => 'alert alert-dismissable alert-success'
            ) ), 201 );
}
else
{
    return new Response( $app['twig']->render( 'code_form.twig', array(
                'message' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut!',
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 201 );
}