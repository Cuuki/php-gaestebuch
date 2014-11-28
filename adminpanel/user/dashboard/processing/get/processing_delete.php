<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $db, $app['session']->get('user') );

foreach($users as $user)
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
if( $role == 'adm' )
{
        return new Response('Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.
                <br><a href="'.$app['url_generator']->generate('dashboard').'">Zurück zur Übersicht</a>', 404);
}
// User aus Session und User aus löschen stimmen überein -> nicht selbst löschen
elseif ( $usernameSession == $usernameSelected )
{
        return new Response( 'Sie können sich nicht selbst löschen!
                                <a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 404 );		
}
// Wenn deleteUser 'true' zurück gibt wurde User erfolgreich gelöscht
elseif( deleteUser( $db, $id ) )
{
        return new Response( 'User erfolgreich gelöscht!
                <a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 201 );
}
else
{
        return new Response( 'User konnte nicht gelöscht werden, versuchen sie es erneut!
                <a href="'.$app['url_generator']->generate('delete').'">Zurück</a>', 404 );		
}

