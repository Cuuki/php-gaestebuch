<?php

error_reporting(-1);
ini_set('log_errors', 1);

// Funktionen einbinden
include('functions.php');

$data = array(
		"firstname" => "",
		"lastname" => "",
		"email" => "",
		"textinput" => ""
	);

include_once "inc/dbconfig.php";

if ( isset($_POST["submit"]) )
{	
	// Prüfen ob Formulardaten vorhanden wenn nicht dann raus, sonst weiter
	if ( ! ( isset($_POST["firstname"]) && isset($_POST["lastname"]) && isset($_POST["email"]) && isset($_POST["textinput"]) ) )
	{
		return;
	}

	// Ermittelt die Schnittmenge von Arrays
	$data = array_intersect_key($_POST, $data);

	// array mit Variablen wird !!immer!! erst bei Aufruf übergeben
	$data = sanitizeData( $data );
	
	// Debugging Funktion, als erstes Zähler
	debug("1.", $data);
	// Formular Validierungsfunktion aufrufen
	$invalidInput = validateForm( $data );

	debug("2.", $invalidInput);

	// Prüfen ob ungültige Eingaben nicht empty sind, wenn nicht empty dann iteriere invalidInput
	if( ! empty($invalidInput) )
	{
		$errorMessages = getErrorMessages( $invalidInput );
	}
	else
	{
		if( savePosts( $data, $db ) )
		{
			// hinweis, dass gespeichert wurde
			$message = "<p>Ihr Beitrag wurde erfolgreich gespeichert.</p>";
		}
		else
		{
			// fehlermeldung, keine weiterleitung
			$message = "<p>Es ist ein Fehler aufgetreten. Bitte versuchen Sie es erneut.</p>";                             
		}
	}
}

$totalentries = totalEntries($db);

$rowsperpage = 5;

$totalpages = totalPages($totalentries, $rowsperpage);

debug(1, $totalpages);

// aktuelle Seite oder Default
if ( isset($_GET['currentpage']) && is_numeric($_GET['currentpage']) )
{
	$currentpage = (int) $_GET['currentpage'];
}
else
{
	// Nummer von Default-Seite
	$currentpage = 1;
}

if ($currentpage > $totalpages)
{
	// Aktuelle Seite = letzter Seite
	$currentpage = $totalpages;
}
if ($currentpage < 1)
{
	$currentpage = 1;
}

// Header, Content (Posts) und Footer ausgeben
include('inc/header.php');
include('inc/main.php');
include('inc/footer.php');


