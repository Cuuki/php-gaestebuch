<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'email',
            'label_text' => 'Neue E-Mail Adresse',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'email'
        ) ), 201 );
