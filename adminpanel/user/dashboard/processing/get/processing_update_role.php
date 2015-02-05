<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'role',
            'label_text' => 'Neue Rolle',
            'id' => $id,
            'is_active_usermanagement' => true,
            'input_name' => 'role'
        ) ), 201 );
