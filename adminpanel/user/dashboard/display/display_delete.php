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
    return new Response( $app['twig']->render( 'user_delete.twig', array(
                'message' => 'Sie haben nicht die nötigen Rechte um einen Benutzer zu löschen, wenden Sie sich an einen Administrator.',
                'users' => $users,
                'is_active_usermanagement' => true,
                'no_access' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}

return new Response( $app['twig']->render( 'user_delete.twig', array(
            'is_active_usermanagement' => true,
            'users' => getUsersLimit( $app['db'], $rowsperpage, $currentpage ),
            'firstpage' => $firstPage,
            'currentpage' => $currentpage,
            'pagenumber' => $pageNumber,
            'nextpage' => $nextPage,
            'previouspage' => $previousPage,
            'lastpage' => $lastPage,
            'pages_before' => $pagesBefore,
            'pages_after' => $pagesAfter,
        ) ), 201 );
