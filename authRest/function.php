<?php
function deliver_response($status, $status_message, $data = null)
{
    header("HTTP/1.1 $status $status_message");
    header("Content-Type: application/json");

    $response = [
        'status' => $status,
        'status_message' => $status_message,
        'data' => $data
    ];

    echo json_encode($response);
}

function getUser($pdo,$login){
    $stmt = $pdo->prepare("SELECT mdp FROM utilisateurs WHERE login = :login");
    $stmt->execute([':login' => $login]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function check_user_exists($pdo, $login)
{
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM utilisateurs WHERE login = :login");
    $stmt->execute([':login' => $login]);
    $count = $stmt->fetchColumn();
    return $count > 0;
}

function insertUser($pdo, $login, $mdp_hache){
    $stmt = $pdo->prepare("INSERT INTO utilisateurs (login, mdp) VALUES (:login, :mdp)");
    $stmt->execute([':login' => $login, ':mdp' => $mdp_hache]);
}
