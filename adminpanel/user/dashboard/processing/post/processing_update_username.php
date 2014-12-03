<?php

use Symfony\Component\HttpFoundation\Response;

$postdata = array(
    'username' => $username->get( 'username' )
);

$userData = getUser( $db, $id );

foreach ( $userData as $user )
{
    $id = $user['id'];
}

$postdata = $this->sanitizeIndividualFields( $postdata );

$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    $errorMessages = getErrorMessages( $invalidInput );
    
    $render = $app['twig']->render( 'user_update_form.twig', array(
        'label_for' => 'username',
        'label_text' => 'Neuer Benutzername:',
        'id' => $id,
        'input_name' => 'username',
        'errormessages' => $errorMessages
            ) );

    return new Response( $render, 404 );
}
else
{
    include_once USER_DIR . '/dashboard/update.php';
    if ( updateUsername( $db, $postdata['username'], $id ) )
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Der Benutzername wurde geändert.',
            'label_for' => 'username',
            'label_text' => 'Neuer Benutzername:',
            'id' => $id,
            'input_name' => 'username'
                ) );

        return new Response( $render, 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        $render = $app['twig']->render( 'user_update_form.twig', array(
            'message' => 'Die Daten konnten nicht geändert werden!',
            'label_for' => 'username',
            'label_text' => 'Neuer Benutzername:',
            'id' => $id,
            'input_name' => 'username'
                ) );

        return new Response( $render, 404 );
    }
}
