<?php

use Symfony\Component\HttpFoundation\Response;

/**
* @return string
**/
return function()
{
	$form = file_get_contents( __DIR__ . '/../../inc/reset-form.html' );
        
        return new Response( $form );
};