<?php

// Habilita erros para debugging 
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Array para simular um banco de dados
$users = [
    [
        'id' => 1,
        'name' => 'João da Silva',
        'email' => 'joao@exemplo.com'
    ],
    [
        'id' => 2,
        'name' => 'Maria Souza',
        'email' => 'maria@exemplo.com'
    ]
];

// Retorna todos os usuários
function getUsers() {
    global $users;
    return $users;
}

// Retorna um usuário específico pelo ID
function getUser($id) {
    global $users;
    $user = array_filter($users, fn($user) => $user['id'] === $id);
    return reset($user); 
}

// Adiciona um novo usuário
function addUser($name, $email) {
    global $users;
    $lastId = end($users)['id'];
    $newId = $lastId + 1;

    $newUser = [
        'id' => $newId,
        'name' => $name,
        'email' => $email
    ];

    $users[] = $newUser;

    return $newUser;
}

// Atualiza um usuário existente
function updateUser($id, $name, $email) {
    global $users;
    $user = getUser($id);
    
    if($user) {
        $user['name'] = $name;
        $user['email'] = $email;
        
        return $user;
    }
    
    return null;
}

// Remove um usuário pelo ID
function deleteUser($id) {
    global $users;
    $index = array_search(getUser($id), $users);
    unset($users[$index]);
}

// Retorna uma resposta JSON 
function sendJSON($data) {
    header('Content-Type: application/json');
    echo json_encode($data);
}

// Verifica o método de requisição 
$method = $_SERVER['REQUEST_METHOD'];

// Processa a requisição baseado no método
switch($method) {
    case 'GET':
        // Retorna todos os usuários
        if(!isset($_GET['id'])) {
            sendJSON(getUsers());
        } 
        // Retorna um usuário específico
        else { 
            $id = intval($_GET['id']);
            sendJSON(getUser($id)); 
        }
        break;
    case 'POST':
        // Adiciona um novo usuário
        $userData = json_decode(file_get_contents('php://input'));
        $name = $userData->name;
        $email = $userData->email;
        sendJSON(addUser($name, $email));
        break;
    case 'PUT': 
        // Atualiza um usuário
        parse_str(file_get_contents('php://input'), $_PUT);
        $id = $_PUT['id'];
        $name = $_PUT['name'];
        $email = $_PUT['email'];
        sendJSON(updateUser($id, $name, $email));
        break;
    case 'DELETE':
        // Remove um usuário
        $id = intval($_GET['id']);
        deleteUser($id);
        sendJSON(['status' => 'ok']);
        break;
}