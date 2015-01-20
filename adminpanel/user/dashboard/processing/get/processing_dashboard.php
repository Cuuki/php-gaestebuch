<?php

use Symfony\Component\HttpFoundation\Response;

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'dashboard.twig', array(
    'loggeduser' => $loggeduser
) );

return new Response( $render, 201 );
