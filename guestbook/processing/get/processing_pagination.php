<?php

$rowsperpage = 5;

$lastPage = totalPages( $totalentries, $rowsperpage );

// aktuelle Seite oder Default
if ( null !== $currentpage->get( 'currentpage' ) && is_numeric( $currentpage->get( 'currentpage' ) ) )
{
    $currentpage = (int) $currentpage->get( 'currentpage' );
}
else
{
    // Nummer von Default-Seite
    $currentpage = 1;
}

if ( $currentpage > $lastPage )
{
    // Aktuelle Seite = letzter Seite
    $currentpage = $lastPage;
}
if ( $currentpage < 1 )
{
    $currentpage = 1;
}

// range of num links to show
$range = 1;
$pageNumber = array();

// loop to show links to range of pages around current page
for ( $pagenum = ($currentpage - $range); $pagenum < (($currentpage + $range) + 1); $pagenum++ )
{   
    if ( ($pagenum > 1) && ($pagenum < $lastPage) )
    {
        array_push( $pageNumber, (string) $pagenum );   
    }
}

$nextPage = $currentpage + 1;
$previousPage = $currentpage - 1;
$firstPage = 1;

$pagesBefore = $currentpage - $range - 1 - $firstPage;
$pagesAfter = $lastPage - $currentpage - $range - 1;
