<?php

/**
* @return string
**/
function getCodeForm ()
{
	return file_get_contents(__DIR__ . '/../../inc/templates/code_form.html');
}