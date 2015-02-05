<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'useremail' => $useremail->get( 'useremail' )
);

$postdata = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'useremail',
                'label_text' => 'Neue E-Mail Adresse',
                'id' => $id,
                'input_name' => 'useremail',
                'is_active_usermanagement' => true,
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    foreach ( getAllUsers( $app['db'] ) as $user )
    {
        $useremails[] = $user['useremail'];
    }

    if ( in_array( $postdata['useremail'], $useremails, true ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Benutzer existiert bereits.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'useremail',
                    'label_text' => 'Neue E-Mail Adresse',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'useremail'
                ) ), 404 );
    }
    elseif ( updateEmail( $app['db'], $postdata['useremail'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die E-Mail Adresse wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'useremail',
                    'label_text' => 'Neue E-Mail Adresse',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'useremail'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'useremail',
                    'label_text' => 'Neue E-Mail Adresse',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'useremail'
                ) ), 404 );
    }
}

