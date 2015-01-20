<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'firstname' => $firstname->get( 'firstname' ),
    'lastname' => $lastname->get( 'lastname' ),
    'email' => $email->get( 'email' ),
    'textinput' => $textinput->get( 'textinput' )
);

include_once POST_DIR . '/update.php';

$entryData = getEntry( $app['db'], $id );
$oldcontent = $entryData['content'];

$postdata = sanitizeData( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) || $postdata['textinput'] == $oldcontent )
{
    $errorMessages = getErrorMessages( $invalidInput );

    $render = $app['twig']->render( 'post_update_id.twig', array(
        'errormessages' => $errorMessages,
        'postdata' => $postdata
            ) );

    return new Response( $render, 404 );
}
else
{
    if ( updatePost( $app['db'], $postdata, $id ) )
    {
        $render = $app['twig']->render( 'post_update_id.twig', array(
            'message' => 'Die Daten wurden geändert!',
            'message_type' => 'successmessage'
                ) );
        
        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'post_update_id.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage'
                ) );
        
        return new Response( $render, 404 );
    }
}
