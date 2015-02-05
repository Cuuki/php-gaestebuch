<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/update.php';

$postdata = array(
    'username' => $username->get( 'username' ),
    'useremail' => $useremail->get( 'useremail' ),
    'password' => $password->get( 'password' )
);

$postdata = $this->sanitizeLogindata( $postdata );
$invalidInput = validateForm( $postdata );

$userData = getUser( $app['db'], $id );

if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'user_update_id.twig', array(
                'user' => $userData,
                'errormessages' => getErrorMessages( $invalidInput ),
                'postdata' => $postdata,
                'is_active_usermanagement' => true,
                'headline' => 'Alles bearbeiten',
                'submitvalue' => 'Ändern'
            ) ), 404 );
}
else
{
    foreach ( getAllUsers( $app['db'] ) as $user )
    {
        $usernames[] = $user['username'];
        $useremails[] = $user['useremail'];
    }

    if ( in_array( $postdata['username'], $usernames, true ) || in_array( $postdata['useremail'], $useremails, true ) )
    {
        return new Response( $app['twig']->render( 'user_update_id.twig', array(
                    'message' => 'Der Benutzer existiert bereits.',
                    'user' => $userData,
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'is_active_usermanagement' => true,
                    'headline' => 'Alles bearbeiten',
                    'submitvalue' => 'Anlegen'
                ) ), 404 );
    }
    elseif ( updateUser( $app['db'], $postdata, $userData['id'] ) )
    {
        return new Response( $app['twig']->render( 'user_update_id.twig', array(
                    'message' => 'Die Daten wurden geändert!',
                    'user' => $userData,
                    'message_type' => 'alert alert-dismissable alert-success',
                    'is_active_usermanagement' => true,
                    'headline' => 'Alles bearbeiten',
                    'submitvalue' => 'Ändern'
                ) ), 201 );
    }
    // Wenn User in Datenbank schon so existiert wie das geänderte Meldung ausgeben weil dieser nicht mehrfach vorkommen darf
    else
    {
        return new Response( $app['twig']->render( 'user_update_id.twig', array(
                    'message' => 'Die Daten konnten nicht geändert werden.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'is_active_usermanagement' => true,
                    'headline' => 'Alles bearbeiten',
                    'submitvalue' => 'Ändern'
                ) ), 404 );
    }
}
