<?php

$firstPage = '';
$pageNumber = array();
$nextPage = '';
$lastPage = '';

$totalentries = totalEntries( $app['db'] );

$rowsperpage = 5;

$totalpages = totalPages( $totalentries, $rowsperpage );

// aktuelle Seite oder Default
if ( isset( $_GET['currentpage'] ) && is_numeric( $_GET['currentpage'] ) )
{
    $currentpage = (int) $_GET['currentpage'];
}
else
{
    // Nummer von Default-Seite
    $currentpage = 1;
}

if ( $currentpage > $totalpages )
{
    // Aktuelle Seite = letzter Seite
    $currentpage = $totalpages;
}
if ( $currentpage < 1 )
{
    $currentpage = 1;
}

if ( $currentpage > 1 )
{
    $firstPage = '?currentpage=1';
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
            $currentPage = $pagenum;
        }
        else
        {
            array_push( $pageNumber, (string) $pagenum );
        }
    }
}

// if not on last page, show forward and last page links
if ( $currentpage != $totalpages )
{
    $nextpage = $currentpage + 1;
    $nextPage = '?currentpage=' . $nextpage;
    $lastPage = '?currentpage=' . $totalpages;
}