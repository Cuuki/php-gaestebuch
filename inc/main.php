<?php
    $value = array(
        'firstname' => '',
        'lastname' => '',
        'email' => '',
        'textinput' => ''
    );
?>

<?php
    include_once __DIR__ . '/header.html';
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

<?php
    include_once __DIR__ . '/footer.html';
?>