<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $db, $app['session']->get( 'user' ) );

foreach ( $users as $user )
{
    $usernameSession = $user['username'];
    $role = $user['role'];
}

$selectedUser = getUser( $db, $id );

foreach ( $selectedUser as $select )
{
    $usernameSelected = $select['username'];
}

include_once USER_DIR . '/dashboard/delete.php';

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'headline' => 'Benutzer löschen:',
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.'
            ) );

    return new Response( $render, 404 );
}
// User aus Session und User aus löschen stimmen überein -> nicht selbst löschen
elseif ( $usernameSession == $usernameSelected )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'headline' => 'Benutzer löschen:',
        'message' => 'Sie können sich nicht selbst löschen!'
            ) );

    return new Response( $render, 404 );
}
// Wenn deleteUser 'true' zurück gibt wurde User erfolgreich gelöscht
elseif ( deleteUser( $db, $id ) )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'message' => 'User erfolgreich gelöscht!',
        'headline' => 'Benutzer löschen:'
            ) );

    return new Response( $render, 201 );
}
else
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'message' => 'User konnte nicht gelöscht werden, versuchen sie es erneut!',
        'headline' => 'Benutzer löschen:'
            ) );

    return new Response( $render, 404 );
}
