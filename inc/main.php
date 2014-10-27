<?php
	$value = array(
		"firstname" => "",
		"lastname" => "",
		"email" => "",
		"textinput" => ""
	);
?>

<main>
	<section id="posts">
		<article>
			<h2 class="articleHeading"><a name="posts">Letzte Beiträge:</a></h2>
		</article>
		<?php
			if ( isset($message) )
			{
				echo $message;
			}

			$posts = getPosts( $db, $rowsperpage, $currentpage );

			// Wenn keine Posts vorhanden
			if( !$posts )
			{
				debug("3 ", $posts);
			    echo "<p>Keine Beiträge vorhanden</p>";
			}
			elseif( !isset( $errorMessages ) )
			{
			  echo displayPosts( $posts );
			  include('lib/pagination.php');
			  echo displayPagination( $currentpage, $totalpages );
			}
		?>
	</section>
	<section id="form">
		<article>
			<h2 class="articleHeading"><a name="add">Tragen Sie sich ein:</a></h2>
			
			<?php
				include('inc/guestbookform.php');	
			?>

		</article>
	</section>			
</main>