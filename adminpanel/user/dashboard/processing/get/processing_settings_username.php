<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_update_form.twig', array(
    'oldinput_for' => 'oldusername',
    'oldinput_text' => 'Alter Benutzername',
    'oldinput_name' => 'oldusername',
    'newinput_for' => 'username',
    'newinput_text' => 'Neuer Benutzername',
    'newinput_name' => 'username'
        ) );

return new Response( $render, 201 );
