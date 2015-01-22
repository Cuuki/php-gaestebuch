<?php

use Symfony\Component\HttpFoundation\Response;

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

$render = $app['twig']->render( 'guestbook.twig', array(
    'posts' => $posts,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'previouspage' => $previousPage,
    'lastpage' => $lastPage,
        ) );

// Header, Content (Posts) und Footer ausgeben
return new Response( $render, 201 );
