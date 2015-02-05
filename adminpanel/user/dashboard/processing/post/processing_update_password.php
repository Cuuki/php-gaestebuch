<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'password' => $password->get( 'password' )
);

$postdata = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'password',
                'label_text' => 'Neues Passwort',
                'id' => $id,
                'input_name' => 'password',
                'is_active_usermanagement' => true,
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    if ( updatePassword( $app['db'], $postdata['password'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Das Passwort wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'password',
                    'label_text' => 'Neues Passwort',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'password'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'password',
                    'label_text' => 'Neues Passwort',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'password'
                ) ), 404 );
    }
}
