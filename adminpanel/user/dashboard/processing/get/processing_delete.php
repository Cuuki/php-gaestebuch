<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/delete.php';
include_once USER_DIR . '/../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'user' );
include_once USER_DIR . '/../../guestbook/processing/get/processing_pagination.php';

// Daten für gerade eingeloggten User aus Datenbank holen
$userSession = getLogindata( $app['db'], $app['session']->get( 'user' ) );
$selectedUser = getUser( $app['db'], $id );
$getAllUsers = getUsersLimit( $app['db'], $rowsperpage, $currentpage );

// Wenn die Benutzerrolle 'Editor' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $userSession['role'] == 'Editor' )
{
    return new Response( $app['twig']->render( 'user_delete.twig', array(
                'users' => $getAllUsers,
                'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.',
                'is_active_usermanagement' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
// User aus Session und User aus löschen stimmen überein -> nicht selbst löschen
elseif ( $userSession['username'] == $selectedUser['username'] )
{
    return new Response( $app['twig']->render( 'user_delete.twig', array(
                'users' => $getAllUsers,
                'message' => 'Sie können sich nicht selbst löschen!',
                'is_active_usermanagement' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}
// Wenn deleteUser 'true' zurück gibt wurde User erfolgreich gelöscht
elseif ( deleteUser( $app['db'], $id ) )
{
    return new Response( $app['twig']->render( 'user_delete.twig', array(
                'users' => $getAllUsers,
                'message' => 'User erfolgreich gelöscht!',
                'message_type' => 'alert alert-dismissable alert-success',
                'is_active_usermanagement' => true,
                'headline' => 'Benutzer löschen:'
            ) ), 201 );
}
else
{
    return new Response( $app['twig']->render( 'user_delete.twig', array(
                'users' => $getAllUsers,
                'message' => 'User konnte nicht gelöscht werden, versuchen sie es erneut!',
                'message_type' => 'alert alert-dismissable alert-danger',
                'is_active_usermanagement' => true,
                'headline' => 'Benutzer löschen:'
            ) ), 404 );
}
