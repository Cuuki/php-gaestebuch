<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_update_form.twig', array(
    'headline' => 'E-Mail ändern:',
    'oldinput_for' => 'oldemail',
    'oldinput_text' => 'Alte E-Mail Adresse:',
    'oldinput_name' => 'oldemail',
    'newinput_for' => 'email',
    'newinput_text' => 'Neue E-Mail Adresse:',
    'newinput_name' => 'email'
        ) );

return new Response( $render, 201 );
