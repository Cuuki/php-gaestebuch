<?php

/**
* @return string
**/
function getCodeForm ()
{
	return file_get_contents(__DIR__ . '/../../inc/code-form.html');
}