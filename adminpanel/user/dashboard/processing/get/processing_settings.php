<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings.twig' );

return new Response( $render, 201 );
