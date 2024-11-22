<?php
// Incluir la conexión a la base de datos
include('db.php');

// Habilitar CORS
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Manejo de preflight (solicitudes OPTIONS)
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Capturar la ruta solicitada
$request = $_SERVER['REQUEST_URI'];
$request = strtok($request, '?');

// Obtener el método HTTP
$method = $_SERVER['REQUEST_METHOD'];

switch ($request) {
    case '/usuarios':
        if ($method === 'GET') {
            // Obtener lista de usuarios
            try {
                $sql = "SELECT id_usuario, nombre, apellidos FROM usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($usuarios);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al obtener usuarios: " . $e->getMessage()]);
            }
        } elseif ($method === 'POST') {
            // Crear nuevo usuario
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['nombre'], $data['apellidos'], $data['edad'], $data['telefono'], $data['usuario'], $data['contrasena'])) {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos"]);
                exit();
            }

            try {
                $sql = "INSERT INTO usuario (nombre, apellidos, edad, telefono, usuario, contrasena) 
                        VALUES (:nombre, :apellidos, :edad, :telefono, :usuario, :contrasena)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([ 
                    ':nombre' => $data['nombre'],
                    ':apellidos' => $data['apellidos'],
                    ':edad' => $data['edad'],
                    ':telefono' => $data['telefono'],
                    ':usuario' => $data['usuario'],
                    ':contrasena' => $data['contrasena'], 
                ]);

                http_response_code(201);
                echo json_encode(["message" => "Usuario creado con éxito"]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear usuario: " . $e->getMessage()]);
            }
        } else {
            http_response_code(405); // Método no permitido
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;

    case '/cita':
        if ($method === 'GET') {
            // Obtener lista de citas
            try {
                $sql = "SELECT c.id_cita, c.fecha, u.nombre 
                        FROM cita c INNER JOIN usuario u ON c.id_usuario = u.id_usuario";
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $citas = $stmt->fetchAll(PDO::FETCH_ASSOC);

                echo json_encode($citas);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al obtener citas: " . $e->getMessage()]);
            }
        } elseif ($method === 'POST') {
            // Crear nueva cita
            $data = json_decode(file_get_contents("php://input"), true);

            if (!isset($data['id_usuario'], $data['fecha'])) {
                http_response_code(400);
                echo json_encode(["error" => "Datos incompletos"]);
                exit();
            }

            try {
                $sql = "INSERT INTO cita (id_usuario, fecha) VALUES (:id_usuario, :fecha)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([ 
                    ':id_usuario' => $data['id_usuario'],
                    ':fecha' => $data['fecha'],
                ]);

                http_response_code(201);
                echo json_encode(["message" => "Cita creada con éxito"]);
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al crear cita: " . $e->getMessage()]);
            }
        } else {
            http_response_code(405); // Método no permitido
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;


            /*DESDE AQUI ME DA EL ERROR NO DEJA ELIMINAR LA CITA SELECCIONADA Y PUES ES PORQUE AL INTENTAR ELIMINAR NO LO ENCUENTRA*/
    case '/cita/': // Ruta para eliminar una cita específica por id_cita
        if ($method === 'DELETE') {
            // Obtener el id_cita de la URL (por ejemplo, /cita/{id_cita})
            $id_cita = basename($_SERVER['REQUEST_URI']); // Obtiene el último segmento de la URL

            if (!$id_cita) {
                http_response_code(400);
                echo json_encode(["error" => "ID de cita no proporcionado"]);
                exit();
            }

            try {
                // Eliminar la cita por su ID
                $sql = "DELETE FROM cita WHERE id_cita = :id_cita";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([':id_cita' => $id_cita]);

                if ($stmt->rowCount() > 0) {
                    http_response_code(200);
                    echo json_encode(["message" => "Cita eliminada con éxito"]);
                } else {
                    http_response_code(404);
                    echo json_encode(["error" => "Cita no encontrada"]);
                }
            } catch (PDOException $e) {
                http_response_code(500);
                echo json_encode(["error" => "Error al eliminar cita: " . $e->getMessage()]);
            }
        } else {
            http_response_code(405); // Método no permitido
            echo json_encode(["error" => "Método no permitido"]);
        }
        break;

    default:
        http_response_code(404);
        echo json_encode(["error" => "Endpoint no válido"]);
        break;
}
