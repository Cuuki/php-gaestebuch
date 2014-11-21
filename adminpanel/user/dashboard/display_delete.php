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
    return new Response( 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.
                <br><a href="' . $app['url_generator']->generate( 'dashboard' ) . '">Zurück zur Übersicht</a>', 404 );
}

include_once USER_DIR . '/../../lib/pagination.php';

$totalentries = totalEntries( $db );

// Anzahl an angezeigen Einträgen pro Seite
$rowsperpage = 5;

$totalpages = totalPages( $totalentries, $rowsperpage );

// aktuelle Seite oder Default
if ( isset( $_GET['currentpage'] ) && is_numeric( $_GET['currentpage'] ) )
{
    $currentpage = (int) $_GET['currentpage'];
}
else
{
    // Nummer von Default-Seite
    $currentpage = 1;
}

if ( $currentpage > $totalpages )
{
    // Aktuelle Seite = letzte Seite
    $currentpage = $totalpages;
}
if ( $currentpage < 1 )
{
    $currentpage = 1;
}

$getAllUsers = getAllUsers( $db );

include_once USER_DIR . '/dashboard/delete.php';
$displayDeleteUsers = displayDeleteUsers( $getAllUsers );

return new Response( $userHeader . displayPagination( $currentpage, $totalpages ) . $displayDeleteUsers .
            '<a href="' . $app['url_generator']->generate( 'dashboard' ) . '">Zurück zur Übersicht</a>', 201 );