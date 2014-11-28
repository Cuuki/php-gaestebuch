<?php

use Symfony\Component\HttpFoundation\Response;

$loggedInSince = $app['session']->get( 'time' );

$render = $app['twig']->render( 'dashboard_form.html' );

//TODO: Sie sind eingeloggt seid ins Template
return new Response( '<p>Sie sind eingeloggt seid: ' . date( 'h:i:sa, d.m.Y', $loggedInSince ) . '</p>' . $render, 201 );
