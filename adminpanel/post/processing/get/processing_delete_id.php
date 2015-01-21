<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/delete.php';

if ( deletePost( $app['db'], $id ) )
{
    $render = $app['twig']->render( 'post_delete.twig', array(
        'message' => 'Beitrag erfolgreich gelöscht!',
        'is_active_postmanagement' => true,
        'message_type' => 'successmessage'
            ) );

    return new Response( $render, 201 );
}
else
{
    $render = $app['twig']->render( 'post_delete.twig', array(
        'message' => 'Der Beitrag konnte nicht gelöscht werden, versuchen sie es erneut!',
        'is_active_postmanagement' => true,
        'message_type' => 'failuremessage'
            ) );

    return new Response( $render, 404 );
}