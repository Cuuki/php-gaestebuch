<?php

use Symfony\Component\HttpFoundation\Response;

return function ()
{
    $form = file_get_contents(__DIR__ . '/../../inc/templates/settings_update_form.html');

    return new Response( $form );
};