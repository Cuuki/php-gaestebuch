<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$usernameSession = $users['username'];
$role = $users['role'];

$selectedUser = getUser( $app['db'], $id );

$usernameSelected = $selectedUser['username'];

include_once USER_DIR . '/dashboard/delete.php';

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'headline' => 'Benutzer löschen:',
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.',
        'is_active_usermanagement' => true,
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
// User aus Session und User aus löschen stimmen überein -> nicht selbst löschen
elseif ( $usernameSession == $usernameSelected )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'headline' => 'Benutzer löschen:',
        'message' => 'Sie können sich nicht selbst löschen!',
        'is_active_usermanagement' => true,
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}
// Wenn deleteUser 'true' zurück gibt wurde User erfolgreich gelöscht
elseif ( deleteUser( $app['db'], $id ) )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'message' => 'User erfolgreich gelöscht!',
        'message_type' => 'successmessage',
        'is_active_usermanagement' => true,
        'headline' => 'Benutzer löschen:'
            ) );

    return new Response( $render, 201 );
}
else
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'message' => 'User konnte nicht gelöscht werden, versuchen sie es erneut!',
        'message_type' => 'failuremessage',
        'is_active_usermanagement' => true,
        'headline' => 'Benutzer löschen:'
            ) );

    return new Response( $render, 404 );
}
