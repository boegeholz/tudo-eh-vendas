<?php
include_once 'config.php';
session_start(); ?>
<!DOCTYPE html>
<html>
<head>
  <title>Login de Usuário</title>
  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
  <h2>Login</h2>
  <form action="login.php" method="post">
    <div class="mb-3">
      <label for="email" class="form-label">Email do Usuário</label>
      <input type="text" class="form-control" id="email" name="email" required>
    </div>
    <div class="mb-3">
      <label for="password" class="form-label">Senha</label>
      <input type="password" class="form-control" id="password" name="password" required>
    </div>
    <button type="submit" class="btn btn-primary">Logar</button>
  </form>
</div>

<?php
// login_sqlserver.php
// Assumes a session has been started (session_start();)
// Assumes $conn is the active SQL Server connection object from your db_connect_sqlserver.php file.

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate input
    if (empty($email) || empty($password)) {
        $errors[] = "Usuário e senha são obrigatórios";
    } else {
        // 1. Fetch user from the database
        $sql = "SELECT TOP 1 u.id, u.senha, u.admin, u.cliente_id, c.nome AS cliente_nome FROM usuario u LEFT JOIN cliente c ON c.id = u.cliente_id WHERE u.inativo = 0 AND u.email = ?";
        $params = array($email);
        
        // Execute the query
        $stmt = sqlsrv_query($conn, $sql, $params);

        if ($stmt === false) {
            die("Error executing query: " . print_r(sqlsrv_errors(), true));
        }

        // 2. Check if a user was found
        if (sqlsrv_has_rows($stmt)) {
            // Fetch the user data
            $user = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);
            $hashedPassword = $user['senha'];
            // 3. Verify the password against the hash
            if (password_verify($password, $hashedPassword)) {
                // Password is correct, start the session
                $_SESSION['email'] = $email;
                $_SESSION['admin'] = $user['admin'];
                $_SESSION['cliente'] = $user['cliente_id'] ?? null;
                $_SESSION['nome_cliente'] = $user['cliente_nome'] ?? 'Não vinculado';
                $_SESSION['timezone'] = "America/Sao_Paulo";

                // Free the statement resource before redirecting
                sqlsrv_free_stmt($stmt);
                echo "<script>window.location.href = 'dashboard.php';</script>";
                exit();
            } else {
                // Incorrect password
                $errors[] = "Usuário ou senha incorretos";
            }
        } else {
            // No user found with that username
            $errors[] = "Usuário ou senha incorretos";
        }
        
        // Free the statement resource
        sqlsrv_free_stmt($stmt);
    }
}

// Display errors if any
if (!empty($errors)) {
    echo '<div class="alert alert-danger"><ul>';
    foreach ($errors as $e) {
        echo "<li>" . htmlspecialchars($e) . "</li>";
    }
    echo '</ul></div>';
}
?>
