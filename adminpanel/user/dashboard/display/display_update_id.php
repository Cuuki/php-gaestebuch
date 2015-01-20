<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$role = $users['role'];

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_update_id.twig', array(
        'headline' => 'Alle Benutzerdaten bearbeiten:',
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.',
            ) );

    return new Response( $render, 404 );
}

include_once USER_DIR . '/dashboard/update.php';
// Ausgewählten Benutzer aus Datenbank holen mit $id aus URL
$userData = getUser( $app['db'], $id );

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'user_update_id.twig', array(
    'user' => $userData,
    'headline' => 'Alle Benutzerdaten bearbeiten:',
    'loggeduser' => $loggeduser,
    'submitvalue' => 'Ändern'
        ) );

return new Response( $render, 201 );
