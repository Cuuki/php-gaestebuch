<?php

if ($currentpage > 1)
{
	echo "<p><a href='" . $_GET['currentpage'] = '?currentpage=1' . "'>Erste Seite</a></p>";
}

// range of num links to show
$range = 3;

// loop to show links to range of pages around current page
for ( $pagenum = ($currentpage - $range); $pagenum < (($currentpage + $range)  + 1); $pagenum++ ) 
{
   if ( ($pagenum > 0) && ($pagenum <= $totalpages) )
   {
      if ($pagenum == $currentpage)
      {
        echo "<p>Aktuelle Seite: $pagenum</p>";
      }
      else
      {
		echo "<p><a href='?currentpage=$pagenum'>Seite $pagenum</a></p>";
      }
   }
}
// if not on last page, show forward and last page links
if ( $currentpage != $totalpages )
{
   $nextpage = $currentpage + 1;
   echo "<p><a href='?currentpage=$nextpage'>Nächste Seite</a></p>";
   echo "<p><a href='?currentpage=$totalpages'>Letzte Seite</a></p>";
}

?>