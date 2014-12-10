<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$entryData = getEntry( $app['db'], $id );

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'post_update_id.twig', array(
    'post' => $entryData,
    'loggeduser' => $loggeduser
) );

return new Response( $render, 201 );
