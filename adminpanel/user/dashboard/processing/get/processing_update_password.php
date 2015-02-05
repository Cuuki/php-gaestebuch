<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'password',
            'label_text' => 'Neues Passwort',
            'id' => $id,
            'is_active_usermanagement' => true,
            'input_name' => 'password'
        ) ), 201 );
