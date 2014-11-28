<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_form.html' );

return new Response( $render, 201 );
