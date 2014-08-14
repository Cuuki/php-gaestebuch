<?php
	$value = array(
		"username" => "",
		"useremail" => "",
		"password" => ""
	);
?>

<main>
	<section id="login">
		<article>
			<h2 class="articleHeading">Login:</h2>
		</article>

		<?php 
			include('inc/loginform.php');
		?>

		<?php
			if ( isset($message) )
			{
				echo $message;
			}
		?>
	</section>	
</main>