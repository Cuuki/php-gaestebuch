<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$role = $users['role'];

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_update_id.twig', array(
        'headline' => 'Alles bearbeiten',
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.',
        'is_active_usermanagement' => true,
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}

include_once USER_DIR . '/dashboard/update.php';
// Ausgewählten Benutzer aus Datenbank holen mit $id aus URL
$userData = getUser( $app['db'], $id );

$render = $app['twig']->render( 'user_update_id.twig', array(
    'user' => $userData,
    'is_active_usermanagement' => true,
    'headline' => 'Alles bearbeiten',
    'submitvalue' => 'Ändern'
        ) );

return new Response( $render, 201 );
