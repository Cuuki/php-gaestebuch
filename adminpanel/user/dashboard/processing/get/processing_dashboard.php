<?php

use Symfony\Component\HttpFoundation\Response;

$loggedInSince = $app['session']->get( 'time' );

$timeOfLogin = date( 'r', $loggedInSince );

$render = $app['twig']->render( 'dashboard.twig', array(
    'loggedinsince' => $timeOfLogin,
    'is_active_dashboard' => true
) );

return new Response( $render, 201 );
