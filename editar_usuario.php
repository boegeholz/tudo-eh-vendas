<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';
if (empty($_SESSION['admin']) || $_SESSION['admin'] === 0) {
    header('Location: dashboard.php');
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    header('Location: lista_usuarios.php');
    exit;
}

// Buscar dados do usuário
$sql = "SELECT id, nome, email, admin, cliente_id FROM usuario WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$usuario = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$usuario) {
    header('Location: lista_usuarios.php');
    exit;
}

// Atualizar dados
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'] ?? '';
    $email = $_POST['email'] ?? '';
    $admin = isset($_POST['admin']) ? 1 : 0;
    $cliente_id = $_POST['cliente_id'] ?? null;

    $sql_up = "UPDATE usuario SET nome = ?, email = ?, admin = ?, cliente_id = ? WHERE id = ?";
    sqlsrv_query($conn, $sql_up, [$nome, $email, $admin, $cliente_id, $id]);
    header('Location: lista_usuarios.php');
    exit;
}

// Buscar clientes para o select
$sql_cli = "SELECT id, nome FROM cliente WHERE inativo = 0 ORDER BY nome";
$stmt_cli = sqlsrv_query($conn, $sql_cli);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
    <h2>Editar Usuário</h2>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Nome</label>
            <input type="text" name="nome" class="form-control" value="<?=htmlspecialchars($usuario['nome'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="<?=htmlspecialchars($usuario['email'])?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Cliente</label>
            <select name="cliente_id" class="form-select">
                <option value="">Não vinculado</option>
                <?php while ($cli = sqlsrv_fetch_array($stmt_cli, SQLSRV_FETCH_ASSOC)): ?>
                    <option value="<?=$cli['id']?>" <?=$usuario['cliente_id'] == $cli['id'] ? 'selected' : ''?>>
                        <?=htmlspecialchars($cli['nome'])?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-check mb-3">
            <input type="checkbox" name="admin" class="form-check-input" id="adminCheck" <?=$usuario['admin'] == 1 ? 'checked' : ''?>>
            <label class="form-check-label" for="adminCheck">Administrador</label>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="lista_usuarios.php" class="btn btn-secondary">Cancelar</a>