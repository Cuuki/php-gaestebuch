<?php

use Symfony\Component\HttpFoundation\Response;

$loggedInSince = $app['session']->get( 'time' );

return new Response( $app['twig']->render( 'dashboard.twig', array(
            'loggedinsince' => date( 'r', $loggedInSince ),
            'is_active_dashboard' => true
        ) ), 201 );
