		<form  action="" method="post">
			<?php
				if( isset( $errorMessages ) )
				{
					$value = array(
						"username" => $_POST["username"],
						"useremail" => $_POST["useremail"],
						"password" => $_POST["password"]
					);
				}
			?>
			<label for="username">
				Benutzername:
			</label>		
			<input type="text" id="username" class="login" name="username" value="<?php echo $value['username']; ?>">
			<?php 
				if ( isset( $errorMessages ) && array_key_exists("username", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["username"] . "</p>";
				}
			?>
			<?php
				if( isset($_GET['registrieren']) )
				{
					echo '<label for="email">';
					echo 'E-Mail Adresse:';
					echo '</label>';
					echo '<input type="text" id="email" class="" name="useremail" value="' . $value["useremail"] . '">';
					if ( isset( $errorMessages ) && array_key_exists("useremail", $errorMessages) )
					{
						echo "<p class='errorMessage'>" . $errorMessages["useremail"] . "</p>";
					}			
				}
			?>

			<label for="password">
				Passwort:
			</label>
			<input type="password" id="password" class="login" name="password" value="<?php echo $value['password']; ?>">
			<?php 
				if ( isset( $errorMessages ) && array_key_exists("password", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["password"] . "</p>";
				}
			?>
			<?php
				if( isset($_GET['registrieren']) )
				{			
					echo '<input class="button" name="register" type="submit" value="Registrieren">';
					echo '<a href="index.php">Einloggen?</a>';
				}
				else
				{
					echo '<input class="button" name="login" type="submit" value="Einloggen">';
					echo '<a href="index.php?registrieren">Registrieren?</a>';
				}
			?>
		</form>

