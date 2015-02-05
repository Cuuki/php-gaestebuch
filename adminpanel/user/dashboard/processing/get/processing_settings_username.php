<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'settings_update_form.twig', array(
            'is_active_settings' => true,
            'oldinput_for' => 'oldusername',
            'oldinput_text' => 'Alter Benutzername',
            'oldinput_name' => 'oldusername',
            'newinput_for' => 'username',
            'newinput_text' => 'Neuer Benutzername',
            'newinput_name' => 'username'
        ) ), 201 );
