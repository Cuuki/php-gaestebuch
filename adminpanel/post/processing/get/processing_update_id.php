<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$entryData = getEntry( $app['db'], $id );

$render = $app['twig']->render( 'post_update_id.twig', array(
    'post' => $entryData
) );

return new Response( $render, 201 );
