<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$postdata = array(
    'email' => $email->get( 'email' )
);

$entrydata = getEntry( $app['db'], $id );

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'email',
                'label_text' => 'Neue E-Mail',
                'id' => $id,
                'is_active_postmanagement' => true,
                'post' => true,
                'input_name' => 'email',
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    if ( $postdata['email'] == $entrydata['email'] )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die E-Mail ist identisch mit der alten.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'email',
                    'label_text' => 'Neue E-Mail',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'email'
                ) ), 404 );
    }
    elseif ( updateEmail( $app['db'], $postdata['email'], $id ) )
    {

        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die E-Mail wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'email',
                    'label_text' => 'Neue E-Mail',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'email'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'email',
                    'label_text' => 'Neue E-Mail',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'email'
                ) ), 404 );
    }
}
