<?php

use Symfony\Component\HttpFoundation\Response;

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

$role = $users['role'];

// Wenn die Benutzerrolle 'adm' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $role == 'adm' )
{
    $render = $app['twig']->render( 'user_update.twig', array(
        'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.',
        'is_active_usermanagement' => true,
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}

include_once USER_DIR . '/../../lib/pagination.php';
include_once USER_DIR . '/../../guestbook/processing/get/processing_pagination.php';

$getAllUsers = getAllUsers( $app['db'] );

$render = $app['twig']->render( 'user_update.twig', array(
    'users' => $getAllUsers,
    'is_active_usermanagement' => true,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'previouspage' => $previousPage,
    'lastpage' => $lastPage
        ) );

return new Response( $render, 201 );
