<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$role = $users['role'];

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_delete.twig', array(
        'headline' => 'Benutzer löschen:',
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.',
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}

include_once USER_DIR . '/../../lib/pagination.php';
include_once USER_DIR . '/../../guestbook/processing/get/processing_pagination.php';

$getAllUsers = getAllUsers( $app['db'] );

$render = $app['twig']->render( 'user_delete.twig', array(
    'headline' => 'Benutzer löschen:',
    'users' => $getAllUsers,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'lastpage' => $lastPage
        ) );

return new Response( $render, 201 );
