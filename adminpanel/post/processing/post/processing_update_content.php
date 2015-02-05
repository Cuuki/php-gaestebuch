<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$postdata = array(
    'textinput' => $content->get( 'content' )
);

$entrydata = getEntry( $app['db'], $id );

$data = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $data );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'content',
                'label_text' => 'Neuer Beitrag',
                'id' => $id,
                'is_active_postmanagement' => true,
                'post' => true,
                'input_name' => 'content',
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    if ( $postdata['textinput'] == $entrydata['content'] )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Beitrag ist identisch mit dem alten.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'content',
                    'label_text' => 'Neuer Beitrag',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'content'
                ) ), 404 );
    }
    elseif ( updateContent( $app['db'], $postdata['textinput'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Beitrag wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'content',
                    'label_text' => 'Neuer Beitrag',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'content'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'content',
                    'label_text' => 'Neuer Beitrag',
                    'id' => $id,
                    'is_active_postmanagement' => true,
                    'post' => true,
                    'input_name' => 'content'
                ) ), 404 );
    }
}
