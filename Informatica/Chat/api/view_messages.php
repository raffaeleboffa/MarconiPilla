<?php
    require_once '../manager.php';

    if (!isset($_SESSION['user_id'])) {
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['mittente']) || !isset($input['destinatario'])) {
        echo json_encode(['error' => 'Dati mancanti']);
        exit;
    }

    prepareQuery("UPDATE messaggi SET visualizzato = 1 WHERE mittente = :mittente AND destinatario = :destinatario", [
        ":mittente" => $input['mittente'],
        ":destinatario" => $input['destinatario']
    ]);

    echo json_encode(['success' => true]);