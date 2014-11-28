<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/update.php';

$entryData = getEntry( $db, $id );

$render = $app['twig']->render( 'post_form.twig' );

return new Response( displayPosts( $entryData ) . $render, 201 );
