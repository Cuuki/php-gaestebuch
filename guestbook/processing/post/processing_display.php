<?php

use Symfony\Component\HttpFoundation\Response;

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

$isLogged = false;
$isActive = false;

if ( $app['session']->get( 'user' ) )
{
    $isLogged = true;
    $isActive = true;
}

$render = $app['twig']->render( 'guestbook.twig', array(
    'headline' => 'Tragen Sie sich ein:',
    'submit_text' => 'Hinzufügen',
    'is_logged_in' => $isLogged,
    'is_active_guestbook' => $isActive,
    'posts' => $posts,
    'firstpage' => $firstPage,
    'currentpage' => $currentpage,
    'pagenumber' => $pageNumber,
    'nextpage' => $nextPage,
    'previouspage' => $previousPage,
    'lastpage' => $lastPage,
    'errormessages' => $errorMessages,
    'message' => $message,
    'message_type' => $messageType,
    'postdata' => $postdata
        ) );

// Header, Content (Posts) und Footer ausgeben
return new Response( $render, 201 );
