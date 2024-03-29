<?php
session_start();
$currentId = $_SESSION['connected_id'];
include './scripts/connexion.php';
?>
<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Actualités</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php include './templates/header.php' ?>
    <div id="wrapper">
        <aside>

            <?php
            $query = "SELECT photo FROM photos WHERE user = '$currentId'";
            $lesInfos = $mysqli->query($query);
            $nomPhoto = $lesInfos->fetch_assoc();
            if (!isset($nomPhoto)) {
            ?>
                <img src="./photos/user.jpg" alt="" />

            <?php
            } else {
            ?>
                <img src="./photos/<?php echo $nomPhoto['photo'] ?>" alt="./photos/user.jpg" />
            <?php
            }
            ?>

            <section>
                <h3>Présentation </h3>
                <p>Sur cette page vous trouverez les derniers messages de
                    tous les utilisateurices du site.</p>
            </section>
        </aside>
        <main>


            <?php

            //verification
            if ($mysqli->connect_errno) {
                echo "<article>";
                echo ("Échec de la connexion : " . $mysqli->connect_error);
                echo ("<p>Indice: Vérifiez les parametres de <code>new mysqli(...</code></p>");
                echo "</article>";
                exit();
            }


            $laQuestionEnSql = "
                    SELECT posts.id, posts.content,
                    posts.created,
                    users.alias as author_name,  
                    users.id as user_id, 
                    GROUP_CONCAT(tags.id, ':' ,tags.label) AS taglist 
                    FROM posts
                    JOIN users ON  users.id=posts.user_id
                    LEFT JOIN posts_tags ON posts.id = posts_tags.post_id  
                    LEFT JOIN tags       ON posts_tags.tag_id  = tags.id 
                    GROUP BY posts.id
                    ORDER BY posts.created DESC  
                    LIMIT 10
                    ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            // Vérification
            if (!$lesInformations) {
                echo "<article>";
                echo ("Échec de la requete : " . $mysqli->error);
                echo ("<p>Indice: Vérifiez la requete  SQL suivante dans phpmyadmin<code>$laQuestionEnSql</code></p>");
                exit();
            }


            while ($post = $lesInformations->fetch_assoc()) {

            ?>
                <article>
                    <h3>
                        <time><?php echo $post['created'] ?></time>
                    </h3>
                    <address><a href="wall.php?user_id=<?php echo $post['user_id'] ?>"> <?php echo $post['author_name'] ?> </a></address>
                    <div>
                        <p class="has-dropcap"><?php echo $post['content'] ?></p>
                    </div>
                    <footer>
                        <?php include './scripts/buttonLikes.php' ?>
                        <?php
                        $hastag = explode(",", $post['taglist']);
                        if (!empty($hastag[0])) {
                            foreach ($hastag as $tag) {
                                list($tagId, $label) = explode(':', $tag)
                        ?>
                                <a href="tags.php?tag_id=<?php echo $tagId ?>"> <?php echo  '#' . $label . " "  ?></a>
                        <?php
                            }
                        }

                        ?>
                    </footer>
                </article>
            <?php

            }
            ?>

        </main>
    </div>
</body>

</html>