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

/**
 * @return string
 */
function displayPagination ( $currentpage, $totalpages )
{
    $firstPage = '';
    $currentPage = '';
    $pageNumber = '';
    $nextPage = '';
    $lastPage = '';

    if ( $currentpage > 1 )
    {
        $firstPage = "<p><a href='" . $_GET['currentpage'] = '?currentpage=1' . "'>Erste Seite</a></p>";
    }

    // range of num links to show
    $range = 3;

    // loop to show links to range of pages around current page
    for ( $pagenum = ($currentpage - $range); $pagenum < (($currentpage + $range) + 1); $pagenum++ )
    {
        if ( ($pagenum > 0) && ($pagenum <= $totalpages) )
        {
            if ( $pagenum == $currentpage )
            {
                $currentPage = "<p>Aktuelle Seite: $pagenum</p>";
            }
            else
            {
                $pageNumber = "<p><a href='?currentpage=$pagenum'>Seite $pagenum</a></p>";
            }
        }
    }
    // if not on last page, show forward and last page links
    if ( $currentpage != $totalpages )
    {
        $nextpage = $currentpage + 1;
        $nextPage = "<p><a href='?currentpage=$nextpage'>Nächste Seite</a></p>";
        $lastPage = "<p><a href='?currentpage=$totalpages'>Letzte Seite</a></p>";
    }

    return $firstPage . $currentPage . $pageNumber . $nextPage . $lastPage;
}