<?php

use Symfony\Component\HttpFoundation\Response;

return function()
{
	$form = file_get_contents( __DIR__ . '/dashboard.html' );

	return new Response( $form );
};
