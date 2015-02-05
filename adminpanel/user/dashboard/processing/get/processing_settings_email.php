<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'settings_update_form.twig', array(
            'is_active_settings' => true,
            'oldinput_for' => 'oldemail',
            'oldinput_text' => 'Alte E-Mail Adresse',
            'oldinput_name' => 'oldemail',
            'newinput_for' => 'email',
            'newinput_text' => 'Neue E-Mail Adresse',
            'newinput_name' => 'email'
        ) ), 201 );
