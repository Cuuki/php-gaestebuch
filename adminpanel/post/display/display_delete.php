<?php

use Symfony\Component\HttpFoundation\Response;

include_once POST_DIR . '/../../lib/pagination.php';

$totalentries = totalEntries( $db );

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

$posts = getPosts( $db, $rowsperpage, $currentpage );

include_once POST_DIR . '/delete.php';

return new Response( displayPagination( $currentpage, $totalpages ) . displayDeleteEntries( $posts ) .
        '<br>' . '<a href="../">Zurück zur Übersicht</a>', 201 );
