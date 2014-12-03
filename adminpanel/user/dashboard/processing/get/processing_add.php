<?php

use Symfony\Component\HttpFoundation\Response;

$loggeduser = $app['session']->get( 'user' );

$render = $app['twig']->render( 'user_add.twig', array(
    'headline' => 'Benutzer hinzufügen:',
    'loggeduser' => $loggeduser,
    'submitvalue' => 'Anlegen'
        ) );

return new Response( $render, 201 );
