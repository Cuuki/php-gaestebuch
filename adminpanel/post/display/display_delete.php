<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/../../lib/pagination.php';
include_once POST_DIR . '/../../guestbook/processing/get/processing_pagination.php';

$posts = getPosts( $db, $rowsperpage, $currentpage );

$render = $app['twig']->render( 'post_delete.twig', array(
    'posts' => $posts,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'lastpage' => $lastPage,
    'csspath' => '../../css/style.css'
        ) );

return new Response( $render, 201 );
