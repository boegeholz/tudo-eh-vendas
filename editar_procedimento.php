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
    header('Location: lista_procedimentos.php');
    exit;
}

// Buscar procedimento
$sql = "SELECT id, descricao FROM procedimento WHERE id = ?";
$stmt = sqlsrv_query($conn, $sql, [$id]);
$proc = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

if (!$proc) {
    header('Location: lista_procedimentos.php');
    exit;
}

// Atualizar procedimento
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $descricao = $_POST['descricao'] ?? '';
    $sql_up = "UPDATE procedimento SET descricao = ? WHERE id = ?";
    sqlsrv_query($conn, $sql_up, [$descricao, $id]);
    header('Location: lista_procedimentos.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Editar Procedimento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="container py-4">
    <h2>Editar Procedimento</h2>
    <form method="post" class="mt-3">
        <div class="mb-3">
            <label class="form-label">Descrição</label>
            <textarea name="descricao" class="form-control" required><?=htmlspecialchars($proc['descricao'], ENT_QUOTES, 'UTF-8')?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        <a href="lista_procedimentos.php" class="btn btn-secondary">Cancelar</a>
    </form>
</div>
</body>
</html>