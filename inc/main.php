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
			<h2 class="articleHeading">Letzte Beiträge:</h2>
		</article>
		<?php
			if ( isset($message) )
			{
				echo $message;
			}

			$posts = getPosts( $db, $rowsperpage, $currentpage );

			// Wenn query fehlgeschlagen ist Fehler ausgeben
			if( !$posts )
			{
				debug("3 ", $posts);
			    header("Location: error.php");
			}

			if( !isset( $errorMessages ) )
			{
			  echo displayPosts( $posts );
			  include('inc/pagination.php');
			}
		?>
	</section>
	<section id="form">
		<article>
			<h2 class="articleHeading">Tragen Sie sich ein:</h2>
			
			<?php
				include('inc/guestbookform.php');	
			?>

		</article>
	</section>			
</main>