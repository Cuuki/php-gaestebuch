
		<form action="" method="post">
			<?php
				if( isset( $errorMessages ) )
				{
					$i = 1;

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
			<input type="text" id="firstName" name="firstname" value="<?php echo $value['firstname']; ?>">
			<?php 
				if ( isset( $errorMessages ) && array_key_exists("firstname", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["firstname"] . "</p>";
				}
			?>
			<label for="lastName">
				Nachname:
			</label>
			<input type="text" id="lastName" name="lastname" value="<?php echo $value['lastname']; ?>">
			<?php
				if ( isset( $errorMessages ) && array_key_exists("lastname", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["lastname"] . "</p>";
				}
			?>
			<label for="userEmail">
				E-Mail Adresse:
			</label>
			<input type="text" id="userEmail" name="email" value="<?php echo $value['email']; ?>">
			<?php
				if ( isset( $errorMessages ) && array_key_exists("email", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["email"] . "</p>";
				}
			?>
			<label for="userTextinput">
				Geben Sie Ihren Beitrag ein:
			</label>

			<textarea id="userTextinput" name="textinput" rows="10" cols="20"><?php 
					echo $value['textinput'];
				?></textarea>
			<?php
				if ( isset( $errorMessages ) && array_key_exists("textinput", $errorMessages) )
				{
					echo "<p class='errorMessage'>" . $errorMessages["textinput"] . "</p>";
				}
			?>
			<input class="button" name="submit" type="submit" value="Hinzufügen">
			<input class="button" type="reset" value="Eingabe löschen">
		</form>

