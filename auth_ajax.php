<?php
header('Content-Type: application/json');
session_start();
include 'db_connect.php';

// Получаем данные из POST-запроса
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['action'])) {
    echo json_encode(['success' => false, 'message' => 'Неверный запрос']);
    exit;
}

switch ($data['action']) {
    case 'login':
        // Проверяем наличие необходимых данных
        if (!isset($data['phone']) || !isset($data['password'])) {
            echo json_encode(['success' => false, 'message' => 'Не все данные предоставлены']);
            exit;
        }

        // Очищаем телефон от всего кроме цифр
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        $password = $data['password'];
        $remember_me = isset($data['remember_me']) ? $data['remember_me'] : false;

        $stmt = mysqli_prepare($conn, "SELECT * FROM customers WHERE phone = ?");
        mysqli_stmt_bind_param($stmt, "s", $phone);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($user = mysqli_fetch_assoc($result)) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['name'];

                if ($remember_me) {
                    $token = bin2hex(random_bytes(32));
                    $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
                    $stmt = mysqli_prepare($conn, "INSERT INTO remember_tokens (user_id, token, expires_at) VALUES (?, ?, ?)");
                    mysqli_stmt_bind_param($stmt, "iss", $user['id'], $token, $expires);
                    mysqli_stmt_execute($stmt);
                    setcookie('remember_token', $token, strtotime('+30 days'), '/', '', true, true);
                }

                echo json_encode(['success' => true]);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'Неверный пароль']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Пользователь с таким номером не найден']);
            exit;
        }
        break;

    case 'edit_profile':
        // Проверяем авторизацию
        if (!isset($_SESSION['user_id'])) {
            echo json_encode(['success' => false, 'message' => 'Вы не авторизованы']);
            exit;
        }
        $uid = $_SESSION['user_id'];
        $name = mysqli_real_escape_string($conn, trim($data['name']));
        $email = mysqli_real_escape_string($conn, trim($data['email']));
        $phone = preg_replace('/[^0-9]/', '', $data['phone']);
        $address = mysqli_real_escape_string($conn, trim($data['address']));
        $city = mysqli_real_escape_string($conn, trim($data['city'] ?? ''));
        $postal_code = mysqli_real_escape_string($conn, trim($data['postal_code'] ?? ''));

        // (Можно добавить проверки на уникальность телефона и почты если нужно!)

        $stmt = mysqli_prepare($conn, "UPDATE customers SET name=?, email=?, phone=?, address=?, city=?, postal_code=? WHERE id=?");
        mysqli_stmt_bind_param($stmt, "ssssssi", $name, $email, $phone, $address, $city, $postal_code, $uid);

        if (mysqli_stmt_execute($stmt)) {
            echo json_encode(['success' => true]);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'Ошибка при сохранении']);
            exit;
        }

    default:
        echo json_encode(['success' => false, 'message' => 'Неизвестное действие']);
        exit;
}

mysqli_close($conn);
?>
