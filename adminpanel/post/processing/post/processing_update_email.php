<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'email' => $email->get( 'email' )
);

$entrydata = getEntry( $app['db'], $id );

$id = $entrydata['id_entry'];

$data = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
    
    $render = $app['twig']->render( 'user_update_form.twig', array(
        'label_for' => 'email',
        'label_text' => 'Neue E-Mail',
        'id' => $id,
        'is_active_postmanagement' => true,
        'post' => true,
        'input_name' => 'email',
        'errormessages' => $errorMessages
            ) );
    
    return new Response( $render, 404 );
}
else
{
    include_once POST_DIR . '/update.php';
    if ( $postdata['email'] == $entrydata['email'] )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die E-Mail ist identisch mit der alten.',
            'message_type' => 'failuremessage',
            'label_for' => 'email',
            'label_text' => 'Neue E-Mail',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'email'
                ) );

        return new Response( $render, 404 );
    }    
    elseif ( updateEmail( $app['db'], $postdata['email'], $id ) )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die E-Mail wurde geändert.',
            'message_type' => 'successmessage',
            'label_for' => 'email',
            'label_text' => 'Neue E-Mail',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'email'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage',
            'label_for' => 'email',
            'label_text' => 'Neue E-Mail',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'email'
                ) );

        return new Response( $render, 404 );
    }
}
