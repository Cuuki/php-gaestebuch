<?php

use Symfony\Component\HttpFoundation\Response;

$render = $app['twig']->render( 'settings_update_form.html', array(
    'oldinput_for' => 'oldpassword',
    'oldinput_text' => 'Altes Passwort:',
    'oldinput_name' => 'oldpassword',
    'newinput_for' => 'password',
    'newinput_text' => 'Neues Passwort:',
    'newinput_name' => 'password'
        ) );

return new Response( $render . '<a href="' . $app['url_generator']->generate( 'settings' ) . '">Zurück zum Profil</a>', 201 );
