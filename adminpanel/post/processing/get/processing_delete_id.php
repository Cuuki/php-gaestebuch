<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/delete.php';
include_once POST_DIR . '/../../lib/pagination.php';
$totalentries = totalEntries( $app['db'], 'guestbook' );
include_once POST_DIR . '/../../guestbook/processing/get/processing_pagination.php';

$posts = getPosts( $app['db'], $rowsperpage, $currentpage );

if ( deletePost( $app['db'], $id ) )
{
    return new Response( $app['twig']->render( 'post_delete.twig', array(
                'posts' => $posts,
                'message' => 'Beitrag erfolgreich gelöscht!',
                'is_active_postmanagement' => true,
                'message_type' => 'alert alert-dismissable alert-success'
            ) ), 201 );
}
else
{
    return new Response( $app['twig']->render( 'post_delete.twig', array(
                'posts' => $posts,
                'message' => 'Der Beitrag konnte nicht gelöscht werden, versuchen sie es erneut!',
                'is_active_postmanagement' => true,
                'message_type' => 'alert alert-dismissable alert-danger'
            ) ), 404 );
}