<?php

use Symfony\Component\HttpFoundation\Response;

return function()
{
	$form = file_get_contents( __DIR__ . '/../../inc/templates/login_form.html' );

	return new Response( $form );
};
