<?php
    header('Content-Type: application/json');
    require_once '../manager.php';

    if (!isset($_SESSION['user_id'])) {
        exit();
    }

    $input = json_decode(file_get_contents('php://input'), true);

    if (!isset($input['mittente']) || !isset($input['destinatario']) || !isset($input['text'])) {
        echo json_encode(['error' => 'Dati mancanti']);
        exit;
    }

    prepareQuery("INSERT INTO messaggi (mittente, destinatario, text, data) VALUES (:mittente, :destinatario, :text, NOW())", [
        ":mittente" => $input['mittente'],
        ":destinatario" => $input['destinatario'],
        ":text" => $input['text']
    ]);

    echo json_encode(['success' => true]);