<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'settings_update_form.twig', array(
            'is_active_settings' => true,
            'oldinput_for' => 'oldpassword',
            'oldinput_text' => 'Altes Passwort',
            'oldinput_name' => 'oldpassword',
            'newinput_for' => 'password',
            'newinput_text' => 'Neues Passwort',
            'newinput_name' => 'password'
        ) ), 201 );
