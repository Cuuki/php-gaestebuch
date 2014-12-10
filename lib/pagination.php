<?php

/**
 * @return int
 */
function totalEntries ( $db )
{
    // wie viele Zeilen hat Tabelle
    $select = "SELECT COUNT(*) as anzahl FROM guestbook";
    $row = $db->fetchColumn( $select );

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
