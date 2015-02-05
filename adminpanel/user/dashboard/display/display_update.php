<?php

use Symfony\Component\HttpFoundation\Response;

include_once USER_DIR . '/../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'user' );
include_once USER_DIR . '/../../guestbook/processing/get/processing_pagination.php';

// Daten für gerade eingeloggten User aus Datenbank holen
$users = getLogindata( $app['db'], $app['session']->get( 'user' ) );

// Wenn die Benutzerrolle 'Editor' ist, darf der Benutzer keinen anderen Benutzer löschen
if ( $users['role'] == 'Editor' )
{
    return new Response( $app['twig']->render( 'user_update.twig', array(
                'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu bearbeiten, wenden Sie sich an einen Administrator.',
                'users' => $users,
                'is_active_usermanagement' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}

return new Response( $app['twig']->render( 'user_update.twig', array(
            'users' => getUsersLimit( $app['db'], $rowsperpage, $currentpage ),
            'is_active_usermanagement' => true,
            'firstpage' => $firstPage,
            'currentpage' => $currentpage,
            'pagenumber' => $pageNumber,
            'nextpage' => $nextPage,
            'previouspage' => $previousPage,
            'lastpage' => $lastPage,
            'pages_before' => $pagesBefore,
            'pages_after' => $pagesAfter,
        ) ), 201 );
