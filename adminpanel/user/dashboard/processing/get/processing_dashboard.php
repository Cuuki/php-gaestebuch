<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'dashboard.twig', array(
    'is_active_dashboard' => true
) );

return new Response( $render, 201 );
