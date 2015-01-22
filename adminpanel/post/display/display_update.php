<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/../../lib/pagination.php';
include_once POST_DIR . '/../../guestbook/processing/get/processing_pagination.php';

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

$render = $app['twig']->render( 'post_update.twig', array(
    'posts' => $posts,
    'is_active_postmanagement' => true,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'previouspage' => $previousPage,
    'lastpage' => $lastPage,
        ) );

return new Response( $render, 201 );
