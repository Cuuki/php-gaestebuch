<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$postdata = array(
    'lastname' => $lastname->get( 'lastname' )
);

$entrydata = getEntry( $app['db'], $id );

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'lastname',
                'label_text' => 'Neuer Nachname',
                'id' => $id,
                'is_active_postmanagement' => true,
                'post' => true,
                'input_name' => 'lastname',
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    if ( $postdata['lastname'] == $entrydata['lastname'] )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Vorname ist identisch mit dem alten.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'lastname',
                    'label_text' => 'Neuer Nachname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'lastname'
                ) ), 404 );
    }
    elseif ( updateLastname( $app['db'], $postdata['lastname'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Nachname wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'lastname',
                    'label_text' => 'Neuer Nachname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'lastname'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'lastname',
                    'label_text' => 'Neuer Nachname',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'lastname'
                ) ), 404 );
    }
}
