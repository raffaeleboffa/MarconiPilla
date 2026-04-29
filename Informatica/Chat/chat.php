<?php
    require_once 'manager.php';

    if (!isset($_SESSION['user_id'])) {
        header('Location: index.php');
        exit();
    }

    $user_id = $_SESSION['user_id'];
    $user = prepareQuery("SELECT * FROM utenti WHERE id = :id", [":id" => $user_id])[0];

    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        unset($_SESSION['message']);
    }
?>

<!DOCTYPE html>
<html lang="it">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>WebLink</title>
        <link rel="stylesheet" href="css/chat.css">
        <link rel="shortcut icon" href="storage/img/icon.svg" type="image/x-icon">
    </head>
    <body>
        <div class="modal" id="modal_profilo">
            <div class="modal_content">
                <form method="post" action="manager.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="update_profile">
                    <p>Nome:</p>
                    <input type="text" name="nome" value="<?php echo $user['nome']; ?>">
                    <p>Cognome:</p>
                    <input type="text" name="cognome" value="<?php echo $user['cognome']; ?>">
                    <p>Username:</p>
                    <input type="text" name="username" value="<?php echo $user['username']; ?>">
                    <p>Email:</p>
                    <input type="email" name="email" value="<?php echo $user['email']; ?>" disabled>
                    <p>Bio:</p>
                    <textarea name="bio"><?php echo $user['bio']; ?></textarea>
                    <p>Profilo:</p>
                    <input type="file" name="profilo" accept="image/*">
                    <button type="submit">Salva</button>
                </form>
                <button onclick="closeModal('modal_profilo')">Chiudi</button>
            </div>
        </div>

        <div class="lista_chat">
            <div class="header">
                <img class="logo" src="storage/img/header.svg">
                <img class="profilo" src="storage/img/profili/<?php echo $user['profilo']; ?>" onclick="openModal('modal_profilo')">
            </div>
            <div class="ricerca">
                <input type="text" placeholder="Cerca contatti...">
                <button>Cerca</button>
            </div>
            <div class="contatti" id="contatti"></div>
        </div>

        <div class="chat"></div>

        <?php 
            if (isset($message)) {
                echo "<div class='notifica'>" . $message . "</div>";
            }
        ?>
        <script src="js/chat.js"></script>
    </body>
</html>