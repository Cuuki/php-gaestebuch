<?php

/**
 * @return int
 */
function totalEntries ( $db, $case )
{
    // wie viele Zeilen hat Tabelle
    switch ( $case )
    {
        case 'guestbook':
            $guestbook = "SELECT COUNT(*) as anzahl FROM guestbook";
            $row = $db->fetchColumn( $guestbook );
            break;

        case 'user':
            $user = "SELECT COUNT(*) as anzahl FROM user";
            $row = $db->fetchColumn( $user );
            break;
    }

    return (int) $row;
}

/**
 * @return float
 */
function totalPages ( $count, $rowsperpage )
{
    // Maximale Seitenzahl berechnen
    return ceil( $count / $rowsperpage );
}
