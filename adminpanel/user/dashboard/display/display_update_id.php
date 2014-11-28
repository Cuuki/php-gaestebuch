<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $db, $app['session']->get( 'user' ) );

foreach ( $users as $user )
{
    $role = $user['role'];
}

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    return new Response( 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.
                <br><a href="' . $app['url_generator']->generate( 'dashboard' ) . '">Zurück zur Übersicht</a>', 404 );
}

include_once USER_DIR . '/dashboard/update.php';
// Ausgewählten Benutzer aus Datenbank holen mit $id aus URL
$userData = getUser( $db, $id );
$displayUser = displayUser( $userData );

$render = $app['twig']->render( 'user_form.twig', array(
    'headline' => 'Alle Benutzerdaten ändern:',
    'submitvalue' => 'Ändern',
    'link_back' => '../update'
        ) );

return new Response( $displayUser . $render, 201 );
