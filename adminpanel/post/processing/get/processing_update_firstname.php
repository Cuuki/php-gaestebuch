<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'firstname',
            'label_text' => 'Neuer Vorname',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'firstname'
        ) ), 201 );
