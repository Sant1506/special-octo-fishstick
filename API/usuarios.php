<?php
if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM Usuario WHERE id_usuario = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT * FROM Usuario");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($result);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO Usuario (nombre, apellidos, edad, telefono, usuario, contrasena) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$data['nombre'], $data['apellidos'], $data['edad'], $data['telefono'], $data['usuario'], $data['contrasena']]);
    echo json_encode(['message' => 'Usuario creado']);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE Usuario SET nombre = ?, apellidos = ?, edad = ?, telefono = ?, usuario = ?, contrasena = ? WHERE id_usuario = ?");
    $stmt->execute([$data['nombre'], $data['apellidos'], $data['edad'], $data['telefono'], $data['usuario'], $data['contrasena'], $id]);
    echo json_encode(['message' => 'Usuario actualizado']);
} elseif ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM Usuario WHERE id_usuario = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => 'Usuario eliminado']);
}
?>
