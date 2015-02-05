<?php

use Symfony\Component\HttpFoundation\Response;

return new Response( $app['twig']->render( 'user_update_form.twig', array(
            'label_for' => 'lastname',
            'label_text' => 'Neuer Nachname',
            'id' => $id,
            'is_active_postmanagement' => true,
            'post' => true,
            'input_name' => 'lastname'
        ) ), 201 );
