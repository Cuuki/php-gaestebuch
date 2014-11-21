<!DOCTYPE html>
<html>

    <head>
        <title>Gästebuch</title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
        <link rel="stylesheet" type="text/css" href="css/style.css">
        <script>var __adobewebfontsappname__ = "code"</script>
        <script src="http://use.edgefonts.net/nova-mono:n4:all;nova-oval:n4:all.js"></script>
    </head>

    <body>


        <div id="wrapper">
            <header>		
                <h1 class="heading">Willkommen in diesem Gästebuch</h1>
            </header>

<?php
$value = array(
    'firstname' => '',
    'lastname' => '',
    'email' => '',
    'textinput' => ''
);
?>

<main>
    <section id='posts'>
        <article>
            <h2 class='articleHeading'><a name='posts'>Letzte Beiträge:</a></h2>
        </article>
        <?php
        if ( isset( $message ) )
        {
            echo $message;
        }

        $posts = getPosts( $db, $rowsperpage, $currentpage );

        // Wenn keine Posts vorhanden
        if ( !$posts )
        {
            echo '<p>Keine Beiträge vorhanden</p>';
        }
        elseif ( !isset( $errorMessages ) )
        {
            echo displayPosts( $posts );

            echo displayPagination( $currentpage, $totalpages );
        }
        ?>
    </section>
    <section id='form'>
        <article>
            <h2 class='articleHeading'><a name='add'>Tragen Sie sich ein:</a></h2>

            <?php
            include_once __DIR__ . '/guestbookform.php';
            ?>

        </article>
    </section>			
</main>
<footer>
    <h2 class="heading">Mehr Informationen:</h2>
    <div class="list">
        <ul>
            <li>
                <a href="#">Lorem</a>
            </li>					
            <li>
                <a href="#">Ipsum</a>
            </li>
            <li>
                <a href="#">Dolor</a>
            </li>
        </ul>
    </div>
</footer>
</div>
</body>

</html>