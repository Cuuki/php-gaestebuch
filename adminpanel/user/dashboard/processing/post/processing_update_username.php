<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'username' => $username->get( 'username' )
);

$postdata = $this->sanitizeIndividualFields( $postdata );
$invalidInput = validateForm( $postdata );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_form.twig', array(
                'label_for' => 'username',
                'label_text' => 'Neuer Benutzername',
                'id' => $id,
                'input_name' => 'username',
                'is_active_usermanagement' => true,
                'errormessages' => getErrorMessages( $invalidInput )
            ) ), 404 );
}
else
{
    foreach ( getAllUsers( $app['db'] ) as $user )
    {
        $usernames[] = $user['username'];
    }

    if ( in_array( $postdata['username'], $usernames, true ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Benutzer existiert bereits.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'username',
                    'label_text' => 'Neuer Benutzername',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'username'
                ) ), 404 );
    }
    elseif ( updateUsername( $app['db'], $postdata['username'], $id ) )
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Der Benutzername wurde geändert.',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'label_for' => 'username',
                    'label_text' => 'Neuer Benutzername',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'username'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_form.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden!',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'label_for' => 'username',
                    'label_text' => 'Neuer Benutzername',
                    'id' => $id,
                    'is_active_usermanagement' => true,
                    'input_name' => 'username'
                ) ), 404 );
    }
}
