<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_update_form.twig', array(
    'oldinput_for' => 'oldpassword',
    'oldinput_text' => 'Altes Passwort',
    'oldinput_name' => 'oldpassword',
    'newinput_for' => 'password',
    'newinput_text' => 'Neues Passwort',
    'newinput_name' => 'password'
        ) );

return new Response( $render, 201 );
