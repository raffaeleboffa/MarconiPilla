<?php
    session_start();

    require_once 'database/config.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] == 'update_profile') {
            $nome = trim($_POST['nome']);
            $cognome = trim($_POST['cognome']);
            $username = strtolower(trim($_POST['username']));
            $bio = trim($_POST['bio']);
            $profilo = $_FILES['profilo'];

            $su = prepareQuery("SELECT * FROM utenti WHERE username = :username", [":username" => $username]);
            if (!empty($su) && $su[0]['id'] != $_SESSION['user_id']) {
                $_SESSION['message'] = "L'username è già in uso da un altro utente.";
                header("Location: chat.php");
                exit();
            }

            if (strlen($nome) == 0 || strlen($username) == 0) {
                $_SESSION['message'] = "Il nome e l'username non possono essere vuoti.";
                header("Location: chat.php");
                exit();
            } else if (strlen($username) > 50) {
                $_SESSION['message'] = "L'username non può essere più lungo di 50 caratteri.";
                header("Location: chat.php");
                exit();
            }

            prepareQuery(
                "UPDATE utenti SET nome = :nome, cognome = :cognome, username = :username, bio = :bio WHERE id = :id",
                [
                    ":nome" => $nome,
                    ":cognome" => $cognome ? $cognome : null,
                    ":username" => $username,
                    ":bio" => $bio ? $bio : null,
                    ":id" => $_SESSION['user_id']
                ]
            );

            header("Location: chat.php");
            exit();
        }
    }

    function sendMail($to) {
        $subject = "Accesso a WEBLINK";
        $txt = "Ciao!\n\nPer accedere a WEBLINK, utilizza il seguente token privato:\n\n" . generaToken($to) . "\n\nSe ne avevi già uno, quest'ultimo verrà cambiato automaticamente.\n\nGrazie!";
        $headers = "From: raffaeleboffa92@gmail.com" . "\r\n";

        if (mail($to,$subject,$txt,$headers)) {
            return true;
        } else {
            return false;
        }
    }

    function generaToken($email) {
        $value = prepareQuery("SELECT id FROM utenti WHERE email = :email", [":email" => $email]);
        $words = prepareQuery("SELECT * FROM parole");

        if (empty($words)) {
            $token = bin2hex(random_bytes(10));
        } else {
            $token = $words[rand(0, count($words) - 1)]['parola'] . rand(1000, 9999) . $words[rand(0, count($words) - 1)]['parola'];
        }

        if ($value) {
            $userId = $value[0]['id'];
            
            prepareQuery("UPDATE utenti SET token = :token WHERE id = :id", [":token" => password_hash($token, PASSWORD_DEFAULT), ":id" => $userId]);
        } else {
            $lastId = prepareQuery("SELECT id FROM utenti ORDER BY id DESC LIMIT 1");
            $userId = !empty($lastId) ? $lastId[0]['id'] + 1 : 1;
            
            $username = "";

            do {
                $username = "utente" . bin2hex(random_bytes(6));
                $result = prepareQuery("SELECT * FROM utenti WHERE username = :username", [":username" => $username]);
            } while ($result);

            prepareQuery(
                "INSERT INTO utenti (nome, cognome, username, email, token, bio, profilo, data_creazione) VALUES (:nome, :cognome, :username, :email, :token, :bio, :profilo, NOW())",
                [
                    ":nome" => "Utente",
                    ":cognome" => null,
                    ":username" => $username,
                    ":email" => $email,
                    ":token" => password_hash($token, PASSWORD_DEFAULT),
                    ":bio" => null,
                    ":profilo" => "default.svg"
                ]
            );
        }

        return $userId . "#" . $token;
    }