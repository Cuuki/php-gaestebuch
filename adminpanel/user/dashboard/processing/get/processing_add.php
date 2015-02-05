<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_add.twig', array(
            'headline' => 'Benutzer hinzufügen',
            'is_active_usermanagement' => true,
            'submitvalue' => 'Anlegen'
        ) ), 201 );
