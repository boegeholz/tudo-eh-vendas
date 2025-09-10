<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';

if (empty($_SESSION['admin']) || $_SESSION['admin'] === 0) {
    header('Location: dashboard.php');
    exit;
}
// Buscar clientes para o select
$sql_cli = "SELECT id, nome FROM cliente WHERE inativo = 0 ORDER BY nome";
$stmt_cli = sqlsrv_query($conn, $sql_cli);
?>
<!DOCTYPE html>
<html>

<head>
    <title>Registrar Usuário</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5">
        <h2>Registro</h2>
        <form action="registro.php" method="post">
            <div class="mb-3">
                <label for="username" class="form-label">Nome de Usuário</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="pass1" class="form-label">Senha</label>
                <input type="password" class="form-control" id="pass1" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="pass2" class="form-label">Confirmar Senha</label>
                <input type="password" class="form-control" id="pass2" name="confirmar_senha" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Cliente</label>
                <select name="cliente_id" class="form-select">
                    <option value="">Não vinculado</option>
                    <?php while ($cli = sqlsrv_fetch_array($stmt_cli, SQLSRV_FETCH_ASSOC)): ?>
                        <option value="<?= $cli['id'] ?>">
                            <?= htmlspecialchars($cli['nome']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>
            <div class="mb-3">
                <input type="checkbox" class="form-check-input" id="adminCheck" name="admin">
                <label class="form-check-label" for="adminCheck">Administrador</label>
            </div>
            <button type="submit" class="btn btn-primary">Criar Usuário</button>
            <a href="lista_usuarios.php" class="btn btn-secondary">Cancelar</a>
        </form>
    </div>

    <?php
    // registro.php (continuation)
    
    // Initialize an errors array
    $errors = [];

    // Handle form submission
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // Sanitize and retrieve inputs
        $nome = trim($_POST['username']);
        $email = trim($_POST['email']);
        $pass1 = $_POST['senha'];
        $pass2 = $_POST['confirmar_senha'];
        $admin = isset($_POST['admin']) ? 1 : 0;

        // Basic validation
        if (empty($nome)) {
            $errors[] = "Nome é obrigatório";
        }
        if (empty($email)) {
            $errors[] = "Email é obrigatório";
        }
        if (empty($pass1)) {
            $errors[] = "Senha é obrigatória";
        }
        if ($pass1 !== $pass2) {
            $errors[] = "As senhas não coincidem";
        }

        // If no errors, proceed to insert
        if (empty($errors)) {
            // Hash the password securely
            $hashedPassword = password_hash($pass1, PASSWORD_DEFAULT);

            // 1. Check if the username or email already exists
            $sql_check = "SELECT TOP 1 id FROM usuario WHERE nome = ? OR email = ?";
            $params_check = array($nome, $email);

            // Prepare and execute the statement
            $stmt_check = sqlsrv_query($conn, $sql_check, $params_check);

            if ($stmt_check === false) {
                die("Error executing query: " . print_r(sqlsrv_errors(), true));
            }

            // Check if any rows were returned
            if (sqlsrv_has_rows($stmt_check)) {
                // Username or email taken
                $errors[] = "Username or email already exists";
            } else {
                // 2. Insert new user record
                $sql_insert = "INSERT INTO usuario (nome, email, senha, admin, criado_em) VALUES (?, ?, ?, ?, ?)";
                $params_insert = array($nome, $email, $hashedPassword, $admin, date('Y-m-d H:i:s'));

                // Prepare and execute the insert statement
                $stmt_insert = sqlsrv_query($conn, $sql_insert, $params_insert);

                if ($stmt_insert === false) {
                    die("Error inserting user: " . print_r(sqlsrv_errors(), true));
                }

                // Free the statement resource
                sqlsrv_free_stmt($stmt_insert);

                // Registration successful - redirect to login
                header("Location: login.php");
                exit();
            }

            // Free the statement resource
            sqlsrv_free_stmt($stmt_check);
        }
    }

    // Display errors (if any)
    if (!empty($errors)) {
        echo '<div class="alert alert-danger"><ul>';
        foreach ($errors as $e) {
            echo "<li>" . htmlspecialchars($e) . "</li>";
        }
        echo '</ul></div>';
    }
    ?>