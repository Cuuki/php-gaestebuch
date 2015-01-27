<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'textinput' => $content->get( 'content' )
);

$entrydata = getEntry( $app['db'], $id );

$id = $entrydata['id_entry'];

$data = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
    
    $render = $app['twig']->render( 'user_update_form.twig', array(
        'label_for' => 'content',
        'label_text' => 'Neuer Beitrag',
        'id' => $id,
        'is_active_postmanagement' => true,
        'post' => true,
        'input_name' => 'content',
        'errormessages' => $errorMessages
            ) );
    
    return new Response( $render, 404 );
}
else
{
    include_once POST_DIR . '/update.php';
    if ( $postdata['textinput'] == $entrydata['content'] )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Der Beitrag ist identisch mit dem alten.',
            'message_type' => 'failuremessage',
            'label_for' => 'content',
            'label_text' => 'Neuer Beitrag',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'content'
                ) );

        return new Response( $render, 404 );
    }    
    elseif ( updateContent( $app['db'], $postdata['textinput'], $id ) )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Der Beitrag wurde geändert.',
            'message_type' => 'successmessage',
            'label_for' => 'content',
            'label_text' => 'Neuer Beitrag',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'content'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'message_type' => 'failuremessage',
            'label_for' => 'content',
            'label_text' => 'Neuer Beitrag',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'content'
                ) );

        return new Response( $render, 404 );
    }
}
