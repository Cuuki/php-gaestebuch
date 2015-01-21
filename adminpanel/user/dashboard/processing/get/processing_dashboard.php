<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'dashboard.twig' );

return new Response( $render, 201 );
