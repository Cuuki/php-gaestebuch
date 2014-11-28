<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_update_form.html', array(
    'oldinput_for' => 'oldemail',
    'oldinput_text' => 'Alte E-Mail Adresse:',
    'oldinput_name' => 'oldemail',
    'newinput_for' => 'email',
    'newinput_text' => 'Neue E-Mail Adresse:',
    'newinput_name' => 'email'
        ) );

return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
