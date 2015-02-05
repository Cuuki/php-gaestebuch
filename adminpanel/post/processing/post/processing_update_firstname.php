<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$postdata = array(
    'firstname' => $firstname->get( 'firstname' )
);

$entrydata = getEntry( $app['db'], $id );

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'firstname',
                'label_text' => 'Neuer Vorname',
                'id' => $id,
                'is_active_postmanagement' => true,
                'post' => true,
                'input_name' => 'firstname',
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    if ( $postdata['firstname'] == $entrydata['firstname'] )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Vorname ist identisch mit dem alten.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'firstname',
                    'label_text' => 'Neuer Vorname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'firstname'
                ) ), 404 );
    }
    elseif ( updateFirstname( $app['db'], $postdata['firstname'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Vorname wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'firstname',
                    'label_text' => 'Neuer Vorname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'firstname'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'firstname',
                    'label_text' => 'Neuer Vorname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'firstname'
                ) ), 404 );
    }
}
