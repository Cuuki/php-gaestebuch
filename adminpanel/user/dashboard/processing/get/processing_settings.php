<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_form.twig' );

return new Response( $render, 201 );
