<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'useremail' => $useremail->get( 'useremail' )
);

$userData = getUser( $app['db'], $id );

$id = $userData['id'];

$postdata = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );

    $render = $app['twig']->render( 'user_update_form.twig', array(
        'label_for' => 'useremail',
        'label_text' => 'Neue E-Mail Adresse:',
        'id' => $id,
        'input_name' => 'useremail',
        'errormessages' => $errorMessages
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    $userData = getAllUsers( $app['db'] );

    foreach ( $userData as $user )
    {
        $useremail = $user['useremail'];
    }

    if ( $postdata['useremail'] == $useremail )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Der Benutzer existiert bereits.',
            'label_for' => 'useremail',
            'label_text' => 'Neue E-Mail Adresse:',
            'id' => $id,
            'input_name' => 'useremail'
                ) );

        return new Response( $render, 404 );
    }
    elseif ( updateEmail( $app['db'], $postdata['useremail'], $id ) )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die E-Mail Adresse wurde geändert.',
            'label_for' => 'useremail',
            'label_text' => 'Neue E-Mail Adresse:',
            'id' => $id,
            'input_name' => 'useremail'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'label_for' => 'useremail',
            'label_text' => 'Neue E-Mail Adresse:',
            'id' => $id,
            'input_name' => 'useremail'
                ) );

        return new Response( $render, 404 );
    }
}

