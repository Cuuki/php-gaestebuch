<?php

/**
 * @return int
 */
function totalEntries ( mysqli $db )
{
    // wie viele Zeilen hat Tabelle
    $sql = "SELECT COUNT(*) as anzahl FROM guestbook";
    $result = $db->query( $sql );
    $row = mysqli_fetch_row( $result );

    // Soll alle Zeilen als int zurückgeben
    return (int) $row[0];
}

/**
 * @return float
 */
function totalPages ( $count, $rowsperpage )
{
    // Maximale Seitenzahl berechnen
    return ceil( $count / $rowsperpage );
}