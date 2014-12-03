<?php

use Symfony\Component\HttpFoundation\Response;

$loggedInSince = $app['session']->get( 'time' );

$timeOfLogin = date( 'h:i:s a, d.m.Y', $loggedInSince );

$render = $app['twig']->render( 'dashboard_form.twig', array(
    'loggedinsince' => $timeOfLogin
) );

return new Response( $render, 201 );
