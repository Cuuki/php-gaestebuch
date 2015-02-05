<?php

use Symfony\Component\HttpFoundation\Response;

include_once __DIR__ . '/../../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'guestbook' );
include_once __DIR__ . '/processing_pagination.php';

$isLogged = false;
$isActive = false;

if ( $app['session']->get( 'user' ) )
{
    $isLogged = true;
    $isActive = true;
}

// Header, Content (Posts) und Footer ausgeben
return new Response( $app['twig']->render( 'guestbook.twig', array(
            'headline' => 'Tragen Sie sich ein',
            'submit_text' => 'Hinzufügen',
            'is_logged_in' => $isLogged,
            'is_active_guestbook' => $isActive,
            'posts' => getPosts( $app['db'], $rowsperpage, $currentpage ),
            'firstpage' => $firstPage,
            'currentpage' => $currentpage,
            'pagenumber' => $pageNumber,
            'nextpage' => $nextPage,
            'previouspage' => $previousPage,
            'lastpage' => $lastPage,
            'pages_before' => $pagesBefore,
            'pages_after' => $pagesAfter,
        ) ), 201 );
