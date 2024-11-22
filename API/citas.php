<?php
if ($method === 'GET') {
    if ($id) {
        $stmt = $pdo->prepare("SELECT * FROM Cita WHERE id_cita = ?");
        $stmt->execute([$id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        $stmt = $pdo->query("SELECT * FROM Cita");
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    echo json_encode($result);
} elseif ($method === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("INSERT INTO Cita (fecha, hora, id_usuario) VALUES (?, ?, ?)");
    $stmt->execute([$data['fecha'], $data['hora'], $data['id_usuario']]);
    echo json_encode(['message' => 'Cita creada']);
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $stmt = $pdo->prepare("UPDATE Cita SET fecha = ?, hora = ?, id_usuario = ? WHERE id_cita = ?");
    $stmt->execute([$data['fecha'], $data['hora'], $data['id_usuario'], $id]);
    echo json_encode(['message' => 'Cita actualizada']);
} elseif ($method === 'DELETE') {
    $stmt = $pdo->prepare("DELETE FROM Cita WHERE id_cita = ?");
    $stmt->execute([$id]);
    echo json_encode(['message' => 'Cita eliminada']);
}
?>
