<?php

use Symfony\Component\HttpFoundation\Response;

// Wenn bereits eingeloggt weiterleiten auf Dashboard
if ( ($app['session']->get( 'user' )) != NULL )
{
    return $app->redirect( $app['url_generator']->generate( 'dashboard' ) );
}

$render = $app['twig']->render( 'code_form.html' );

return new Response( $render, 201 );
