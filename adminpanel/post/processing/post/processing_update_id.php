<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$postdata = array(
    'firstname' => $firstname->get( 'firstname' ),
    'lastname' => $lastname->get( 'lastname' ),
    'email' => $email->get( 'email' ),
    'textinput' => $textinput->get( 'textinput' )
);

$entryData = getEntry( $app['db'], $id );

$postdata = sanitizeData( $postdata );
$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) || $postdata['textinput'] == $entryData['content'] )
{
    return new Response( $app['twig']->render( 'post_update_id.twig', array(
                'headline' => 'Daten ändern',
                'submit_text' => 'Ändern',
                'errormessages' => getErrorMessages( $invalidInput ),
                'is_active_postmanagement' => true,
                'post' => $entryData,
                'postdata' => $postdata
            ) ), 404 );
}
else
{
    if ( updatePost( $app['db'], $postdata, $id ) )
    {
        return new Response( $app['twig']->render( 'post_update_id.twig', array(
                    'headline' => 'Daten ändern',
                    'submit_text' => 'Ändern',
                    'message' => 'Die Daten wurden geändert!',
                    'is_active_postmanagement' => true,
                    'message_type' => 'alert alert-dismissable alert-success'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'post_update_id.twig', array(
                    'headline' => 'Daten ändern',
                    'submit_text' => 'Ändern',
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'is_active_postmanagement' => true,
                    'message_type' => 'alert alert-dismissable alert-danger'
                ) ), 404 );
    }
}
