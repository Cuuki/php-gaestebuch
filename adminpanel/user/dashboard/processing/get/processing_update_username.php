<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'user_update_form.html', array(
    'label_for' => 'username',
    'label_text' => 'Neuer Benutzername:',
    'input_name' => 'username'
        ) );

return new Response( $render . '<a href="' . $app['url_generator']->generate( 'update' ) . $id . '">Zurück</a>', 201 );
