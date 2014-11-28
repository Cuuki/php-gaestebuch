<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_form.html', array(
    'headline' => 'Benutzer hinzufügen:',
    'submitvalue' => 'Anlegen',
    'link_back' => '../'
        ) );

return new Response( $render, 201 );
