<?php

use Symfony\Component\HttpFoundation\Response;

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

$isLogged = false;

if ( $app['session']->get( 'user' ) )
{
    $isLogged = true;
}

$render = $app['twig']->render( 'guestbook.twig', array(
    'headline' => 'Tragen Sie sich ein:',
    'submit_text' => 'Hinzufügen',
    'is_logged_in' => $isLogged,
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
