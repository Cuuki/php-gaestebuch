<?php

use Symfony\Component\HttpFoundation\Response;

include_once __DIR__ . '/../../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'guestbook' );
include_once __DIR__ . '/../get/processing_pagination.php';

$postdata = array(
    'firstname' => $firstname->get( 'firstname' ),
    'lastname' => $lastname->get( 'lastname' ),
    'email' => $email->get( 'email' ),
    'textinput' => $textinput->get( 'textinput' )
);

$data = sanitizeData( $postdata );
$invalidInput = validateForm( $data );

$isLogged = false;
$isActive = false;

if ( $app['session']->get( 'user' ) )
{
    $isLogged = true;
    $isActive = true;
}

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

// Prüfen ob ungültige Eingaben nicht empty sind, wenn nicht empty dann iteriere invalidInput
if ( !empty( $invalidInput ) )
{
    return new Response( $app['twig']->render( 'guestbook.twig', array(
                'headline' => 'Tragen Sie sich ein',
                'submit_text' => 'Hinzufügen',
                'is_logged_in' => $isLogged,
                'is_active_guestbook' => $isActive,
                'firstpage' => $firstPage,
                'currentpage' => $currentpage,
                'pagenumber' => $pageNumber,
                'nextpage' => $nextPage,
                'previouspage' => $previousPage,
                'lastpage' => $lastPage,
                'posts' => $posts,
                'errormessages' => getErrorMessages( $invalidInput ),
                'postdata' => $postdata,
                'pages_before' => $pagesBefore,
                'pages_after' => $pagesAfter,
            ) ), 404 );
}
else
{
    if ( savePosts( $data, $app['db'] ) )
    {
        return new Response( $app['twig']->render( 'guestbook.twig', array(
                    'headline' => 'Tragen Sie sich ein',
                    'submit_text' => 'Hinzufügen',
                    'is_logged_in' => $isLogged,
                    'is_active_guestbook' => $isActive,
                    'firstpage' => $firstPage,
                    'currentpage' => $currentpage,
                    'pagenumber' => $pageNumber,
                    'nextpage' => $nextPage,
                    'previouspage' => $previousPage,
                    'lastpage' => $lastPage,
                    'posts' => $posts,
                    'message' => 'Ihr Beitrag wurde gespeichert!',
                    'message_type' => 'alert alert-dismissable alert-success',
                    'site_one' => '?currentpage=1',
                    'postdata' => $postdata,
                    'pages_before' => $pagesBefore,
                    'pages_after' => $pagesAfter,
                ) ), 201 );
    }
    else
    {
        return new Response( $app['twig']->render( 'guestbook.twig', array(
                    'headline' => 'Tragen Sie sich ein',
                    'submit_text' => 'Hinzufügen',
                    'is_logged_in' => $isLogged,
                    'is_active_guestbook' => $isActive,
                    'firstpage' => $firstPage,
                    'currentpage' => $currentpage,
                    'pagenumber' => $pageNumber,
                    'nextpage' => $nextPage,
                    'previouspage' => $previousPage,
                    'lastpage' => $lastPage,
                    'posts' => $posts,
                    'message' => 'Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.',
                    'message_type' => 'alert alert-dismissable alert-danger',
                    'site_one' => '?currentpage=1',
                    'postdata' => $postdata,
                    'pages_before' => $pagesBefore,
                    'pages_after' => $pagesAfter,
                ) ), 404 );
    }
}



