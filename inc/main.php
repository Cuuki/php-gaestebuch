<?php
	$value = array(
		"firstname" => "",
		"lastname" => "",
		"email" => "",
		"textinput" => ""
	);
?>

<main>
	<section id="login">
		<article>
			<h2 class="articleHeading">Login:</h2>
		</article>		
		<form action="" method="post">

			<label for="username">
				Benutzername:
			</label>		
			<input type="text" id="username" class="login" name="username" value="">
			
		<!--<label for="email">
				E-Mail Adresse:
			</label>
			<input type="text" id="email" class="" name="useremail" value=""> -->

			<label for="password">
				Passwort:
			</label>
			<input type="password" id="password" class="login" name="password" value="">			
			<input class="button" name="submit" type="submit" value="Einloggen">

		</form>	
	</section>
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
				if ($currentpage > 1)
				{
					echo "<p><a href='" . $_GET['currentpage'] = '?currentpage=1' . "'>Zurück zu Seite 1</a></p>";
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
				        echo "<p>Sie befinden sich auf Seite $pagenum</p>";
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
			}
			
		?>
	</section>
	<section id="form">
		<article>
			<h2 class="articleHeading">Tragen Sie sich ein:</h2>
			<form action="" method="post">
				<?php 
					if( isset( $errorMessages ) )
					{
						$value = array(
							"firstname" => $_POST["firstname"],
							"lastname" => $_POST["lastname"],
							"email" => $_POST["email"],
							"textinput" => $_POST["textinput"]
						);
					}
				?>
				<label for="firstName">
					Vorname:
				</label>		
				<input type="text" id="firstName" class="<?php echo $cssclass; ?>" name="firstname" value="<?php echo $value['firstname']; ?>">
				<?php 
					if ( isset( $errorMessages ) && array_key_exists("firstname", $errorMessages) )
					{
						echo "<p class='errorMessage'>" . $errorMessages["firstname"] . "</p>";
					}
				?>
				<label for="lastName">
					Nachname:
				</label>
				<input type="text" id="lastName" class="<?php echo $cssclass; ?>" name="lastname" value="<?php echo $value['lastname']; ?>">
				<?php
					if ( isset( $errorMessages ) && array_key_exists("lastname", $errorMessages) )
					{
						echo "<p class='errorMessage'>" . $errorMessages["lastname"] . "</p>";
					}
				?>
				<label for="userEmail">
					E-Mail Adresse:
				</label>
				<input type="text" id="userEmail" class="<?php echo $cssclass; ?>" name="email" value="<?php echo $value['email']; ?>">
				<?php
					if ( isset( $errorMessages ) && array_key_exists("email", $errorMessages) )
					{
						echo "<p class='errorMessage'>" . $errorMessages["email"] . "</p>";
					}
				?>
				<label for="userTextinput">
					Geben Sie Ihren Beitrag ein:
				</label>

				<textarea id="userTextinput" class="<?php echo $cssclass; ?>" name="textinput" rows="10" cols="20"><?php 
						echo $value['textinput'];
					?></textarea>
				<?php
					if ( isset( $errorMessages ) && array_key_exists("textinput", $errorMessages) )
					{
						echo "<p class='errorMessage'>" . $errorMessages["textinput"] . "</p>";
					}
				?>
				<input class="button" name="submit" type="submit" value="Absenden">
				<input class="button" type="reset" value="Zurücksetzen">
			</form>
		</article>
	</section>			
</main>