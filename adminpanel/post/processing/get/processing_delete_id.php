<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/delete.php';

if ( deletePost( $app['db'], $id ) )
{
    $render = $app['twig']->render( 'post_delete.twig', array(
        'message' => 'Beitrag erfolgreich gelöscht!',
        'message_type' => 'successmessage',
        'csspathone' => '../../../css/bootstrap.min.css',
        'csspathtwo' => '../../../css/styles.css'
            ) );

    return new Response( $render, 201 );
}
else
{
    $render = $app['twig']->render( 'post_delete.twig', array(
        'message' => 'Der Beitrag konnte nicht gelöscht werden, versuchen sie es erneut!',
        'message_type' => 'failuremessage',
        'csspathone' => '../../../css/bootstrap.min.css',
        'csspathtwo' => '../../../css/styles.css'
            ) );

    return new Response( $render, 404 );
}