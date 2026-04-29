<?php
    require_once '../manager.php';

    if (!isset($_SESSION['user_id'])) {
        exit();
    }

    $user_id = $_SESSION['user_id'];

    $m = prepareQuery("SELECT * FROM messaggi WHERE destinatario = :destinatario OR mittente = :mittente ORDER BY data DESC", [
        ":destinatario" => $user_id,
        ":mittente" => $user_id
    ]);
    $u = prepareQuery("SELECT id, nome, cognome, username, bio, profilo FROM utenti");

    $json = [
        "messaggi" => [],
        "utenti" => []
    ];

    foreach ($m as $messaggio) {
        if ($messaggio['mittente'] == $user_id) {
            foreach ($u as $utente) {
                if ($utente['id'] == $messaggio['destinatario']) {
                    $json['messaggi'][] = $messaggio;
                    if (!in_array($utente, $json['utenti'])) {
                        $json['utenti'][] = $utente;
                    }
                    break;
                }
            }
        } else if ($messaggio['destinatario'] == $user_id) {
            foreach ($u as $utente) {
                if ($utente['id'] == $messaggio['mittente']) {
                    $json['messaggi'][] = $messaggio;
                    if (!in_array($utente, $json['utenti'])) {
                        $json['utenti'][] = $utente;
                    }
                    break;
                }
            }
        }
    }

    echo json_encode($json);