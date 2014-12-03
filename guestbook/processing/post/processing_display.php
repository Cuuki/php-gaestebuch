<?php

use Symfony\Component\HttpFoundation\Response;

$posts = getPosts( $db, $rowsperpage, $currentpage );

$render = $app['twig']->render( 'guestbook.twig', array(
    'posts' => $posts,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'lastpage' => $lastPage,
    'errormessages' => $errorMessages,
    'message' => $message,
    'postdata' => $postdata
        ) );

// Header, Content (Posts) und Footer ausgeben
return new Response( $render, 201 );
