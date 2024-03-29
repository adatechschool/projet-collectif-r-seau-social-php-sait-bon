<?php
session_start();
$currentId = $_SESSION['connected_id'];
include './scripts/connexion.php';
?>

<!doctype html>
<html lang="fr">

<head>
    <meta charset="utf-8">
    <title>ReSoC - Paramètres</title>
    <meta name="author" content="Julien Falconnet">
    <link rel="stylesheet" href="style.css" />
</head>

<body>
    <?php include './templates/header.php' ?>

    <div id="wrapper" class='profile'>
        <?php

        $userId = intval($_GET['user_id']);
        ?>
        <aside>
            <?php
            $laQuestionEnSql = "
                SELECT users.*, 
                count(DISTINCT posts.id) as totalpost, 
                count(DISTINCT given.post_id) as totalgiven, 
                count(DISTINCT recieved.user_id) as totalrecieved 
                FROM users 
                LEFT JOIN posts ON posts.user_id=users.id 
                LEFT JOIN likes as given ON given.user_id=users.id 
                LEFT JOIN likes as recieved ON recieved.post_id=posts.id 
                WHERE users.id = '$userId' 
                GROUP BY users.id
                ";
            $lesInformations = $mysqli->query($laQuestionEnSql);
            if (!$lesInformations) {
                echo ("Échec de la requete : " . $mysqli->error);
            }
            $user = $lesInformations->fetch_assoc();
            ?>

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
                <img src="./photos/<?php echo $nomPhoto['photo'] ?>" alt="Portrait de l'utilisateurice" />
            <?php
            }
            ?>
            <p>Changer/Ajouter une photo :</p>
            <form action="./scripts/photo.php" method="POST" enctype="multipart/form-data">
                <label for="file"></label>
                <input type="file" name="file">
                <button type="submit" class="btn-style">Enregistrer</button>
            </form>

            <section>
                <h3>Présentation</h3>
                <p>Sur cette page vous trouverez les informations de l'utilisateurice : <?php echo $user['alias'] ?>
                    <!-- n° <?php intval($_GET['user_id']) ?> -->
                </p>
            </section>
        </aside>
        <main>

            <article class='parameters'>
                <h3>Mes paramètres</h3>
                <dl>
                    <dt>Pseudo</dt>
                    <dd><a href="wall.php?user_id=<?php echo $user['id'] ?>"> <?php echo $user['alias'] ?> </a></dd>
                    <dt>Email</dt>
                    <dd><?php echo $user['email'] ?></dd>
                    <dt>Nombre de messages</dt>
                    <dd><?php echo $user['totalpost'] ?></dd>
                    <dt>Nombre de "J'aime" donnés </dt>
                    <dd><?php echo $user['totalgiven'] ?></dd>
                    <dt>Nombre de "J'aime" reçus</dt>
                    <dd><?php echo $user['totalrecieved'] ?></dd>
                </dl>

            </article>
        </main>
    </div>
</body>

</html>