<?php
    require_once 'manager.php';

    if (isset($_SESSION['user_id'])) {
        header('Location: chat.php');
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] == 'POST'&& isset($_POST['login'])) {
        $email = trim($_POST['email']);
        $token = trim($_POST['token']);

        if (strlen($email) > 0 && strlen($token) > 0) {
            $_SESSION['message'] = "Inserisci solo l'indirizzo email o token privato di accesso.";
        } else if (strlen($email) == 0 && strlen($token) == 0) {
            $_SESSION['message'] = "Inserisci un indirizzo email o token privato di accesso.";
        } else {
            if (strlen($email) > 0) {
                if (sendMail($email)) {
                    $_SESSION['message'] = "Email inviata con successo. Controlla la tua casella di posta e accedi con il token di accesso.";
                } else {
                    $_SESSION['message'] = "Si è verificato un errore durante l'invio dell'email.";
                }
            } else {
                $token = explode("#", $token);
                $user = prepareQuery("SELECT * FROM utenti WHERE id = :id", [":id" => $token[0]]);
                if ($user && count($user) > 0) {
                    if (password_verify($token[1], $user[0]['token'])) {
                        $_SESSION['user_id'] = $user[0]['id'];
                        header('Location: chat.php');
                        exit();
                    } else {
                        $_SESSION['message'] = "Token privato di accesso non valido.";
                    }
                } else {
                    $_SESSION['message'] = "Utente non trovato.";
                }
            }
        }
    }

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
        <title>Weblink</title>
        <link rel="stylesheet" href="css/index.css">
        <link rel="shortcut icon" href="storage/img/icon.svg" type="image/x-icon">
    </head>
    <body>
        <img class="logo" src="storage/img/logo.svg">
        <div class="box">
            <div class="glass"></div>
            <form method="post">
                <input type="email" name="email" placeholder="Inserisci la tua email...">
                <p>O</p>
                <input type="text" name="token" placeholder="Inserisci il tuo token privato...">
                <input type="submit" name="login" value="Accedi">
            </form>
        </div>
        
        <?php 
            if (isset($message)) {
                echo "<div class='notifica'>" . $message . "</div>";
            }
        ?>
    </body>
</html>