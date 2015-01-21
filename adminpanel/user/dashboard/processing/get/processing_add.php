<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_add.twig', array(
    'headline' => 'Benutzer hinzufügen:',
    'submitvalue' => 'Anlegen'
        ) );

return new Response( $render, 201 );
