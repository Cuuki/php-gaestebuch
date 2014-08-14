<?php

error_reporting(-1);
ini_set('log_errors', 1);

/**
* @return ressource
*/
function dbConnect ( array $options )
{
	static $db = null;

	if( $db instanceof mysqli )
	{
		return $db;
	}
	
	// MySql Objekt erzeugen
	$db = new mysqli( $options["Hostname"], $options["Username"], $options["Password"], $options["Databasename"] );

	// Nur bei kritischen Fehlern wie Datenbankverbindung fehlgeschlagen weiterleiten
	// Wenn mysqli_connect_errno() nicht null zurückgibt ist ein Fehler aufgetreten
	if ( mysqli_connect_errno() )
	{
		debug("Datenbankverbindung:", $db);

		if( $db->connect_error )
		{
	    	header("Location: inc/error.php");
		}

	    header("Location: inc/error.php");
	}

	return $db;
}