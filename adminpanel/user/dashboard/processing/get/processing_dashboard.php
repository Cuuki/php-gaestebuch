<?php

use Symfony\Component\HttpFoundation\Response;

$loggedInSince = $app['session']->get( 'time' );

$timeOfLogin = date( 'h:i:s a, d.m.Y', $loggedInSince );

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'dashboard_form.twig', array(
    'loggedinsince' => $timeOfLogin,
    'loggeduser' => $loggeduser
) );

return new Response( $render, 201 );
