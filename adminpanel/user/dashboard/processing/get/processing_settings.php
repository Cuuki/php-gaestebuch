<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'settings.twig', array(
            'is_active_settings' => true,
        ) ), 201 );
