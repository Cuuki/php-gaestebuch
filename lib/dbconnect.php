<?php

/**
 * @return ressource
 */
function dbConnect ( array $options )
{
    static $db = null;

    if ( $db instanceof mysqli )
    {
        return $db;
    }

    // MySql Objekt erzeugen
    $db = new mysqli( $options["Hostname"], $options["Username"], $options["Password"], $options["Databasename"] );

    // Nur bei kritischen Fehlern wie Datenbankverbindung fehlgeschlagen weiterleiten
    // Wenn mysqli_connect_errno() nicht null zurückgibt ist ein Fehler aufgetreten
    if ( mysqli_connect_errno() )
    {
        debug( "Datenbankverbindung:", $db );

        if ( $db->connect_error )
        {
            header( "Location: ../guestbook/error.php" );
        }

        header( "Location: error.php" );
    }

    return $db;
}
