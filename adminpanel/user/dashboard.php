<?php

use Symfony\Component\HttpFoundation\Response;

return function()
{
	$form = file_get_contents( __DIR__ . '/../inc/dashboard-form.html' );

	return new Response( $form );
};
