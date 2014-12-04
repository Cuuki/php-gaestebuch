<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings.twig', array(
    'headline' => 'Wilkommen auf ihrem Profil'
        ) );

return new Response( $render, 201 );
