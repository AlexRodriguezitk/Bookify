<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $url = 'http://localhost/bookify/api/login';
    $data = [
        "username" => "test",
        "password" => "Test123"
    ];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json",
            'method'  => 'POST',
            'content' => json_encode($data)
        ]
    ];

    $context  = stream_context_create($options);
    $response = file_get_contents($url, false, $context);

    if ($response === FALSE) {
        die('Error al conectar con la API.');
    }

    $responseData = json_decode($response, true);

    if ($responseData['status'] && isset($responseData['data']['token'])) {
        $_SESSION['jwt'] = $responseData['data']['token'];
        $_SESSION['message'] = "Login exitoso. Token guardado en sesión.";
    } else {
        $_SESSION['message'] = "Error en el login: " . $responseData['message'];
    }

    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['protected'])) {
    if (!isset($_SESSION['jwt'])) {
        die("No hay token disponible. Inicie sesión primero.");
    }

    $protectedUrl = 'http://localhost/bookify/api/users';
    $authHeader = "Authorization: Bearer " . $_SESSION['jwt'];

    $options = [
        'http' => [
            'header'  => "Content-Type: application/json\r\n" . $authHeader,
            'method'  => 'GET'
        ]
    ];

    $context  = stream_context_create($options);
    $protectedResponse = file_get_contents($protectedUrl, false, $context);

    if ($protectedResponse === FALSE) {
        die('Error al acceder al endpoint protegido.');
    }

    $protectedData = json_decode($protectedResponse, true);
    $_SESSION['protected_response'] = $protectedData;
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
</head>
<body>
    <h2>Login</h2>

    <?php if (isset($_SESSION['message'])): ?>
        <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
    <?php endif; ?>

    <form method="post">
        <button type="submit" name="login">Iniciar Sesión</button>
    </form>

    <?php if (isset($_SESSION['jwt'])): ?>
        <h3>Token guardado en sesión</h3>
        <form method="post">
            <button type="submit" name="protected">Ir a Ruta Protegida</button>
        </form>
    <?php endif; ?>

    <?php if (isset($_SESSION['protected_response'])): ?>
        <h3>Respuesta de la Ruta Protegida</h3>
        <pre><?php print_r($_SESSION['protected_response']); unset($_SESSION['protected_response']); ?></pre>
    <?php endif; ?>
</body>
</html>
