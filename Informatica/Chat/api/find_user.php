<?php
    require_once '../manager.php';

    if (!isset($_SESSION['user_id'])) {
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['username'])) {
        echo json_encode(['error' => 'Dati mancanti']);
        exit;
    }

    $user = prepareQuery("SELECT id, nome, cognome, username, bio, profilo FROM utenti WHERE username = :username", [":username" => $input['username']])[0];

    if (!$user) {
        echo json_encode(['error' => 'Utente non trovato']);
        exit;
    }

    echo json_encode(['success' => true, 'user' => $user]);