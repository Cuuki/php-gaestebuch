<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'guestbook' );
include_once POST_DIR . '/../../guestbook/processing/get/processing_pagination.php';

return new Response( $app['twig']->render( 'post_delete.twig', array(
            'posts' => getPosts( $app['db'], $rowsperpage, $currentpage ),
            'is_active_postmanagement' => true,
            'firstpage' => $firstPage,
            'currentpage' => $currentpage,
            'pagenumber' => $pageNumber,
            'nextpage' => $nextPage,
            'previouspage' => $previousPage,
            'lastpage' => $lastPage,
            'pages_before' => $pagesBefore,
            'pages_after' => $pagesAfter,
        ) ), 201 );
