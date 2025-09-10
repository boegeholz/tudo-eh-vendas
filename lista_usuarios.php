<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';

// Verifica se o usuário está autenticado como admin
if (empty($_SESSION['admin']) || $_SESSION['admin'] === 0) {
    header('Location: dashboard.php');
    exit;
}

// Ativar/desativar usuário
if (isset($_GET['toggle_id'])) {
    $toggle_id = intval($_GET['toggle_id']);
    $sql_check = "SELECT inativo FROM usuario WHERE id = ?";
    $stmt_check = sqlsrv_query($conn, $sql_check, [$toggle_id]);
    $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
    if ($row) {
        $novo_status = ($row['inativo'] == 1) ? 0 : 1;
        $sql_toggle = "UPDATE usuario SET inativo = ? WHERE id = ?";
        sqlsrv_query($conn, $sql_toggle, [$novo_status, $toggle_id]);
        header("Location: lista_usuarios.php");
        exit;
    }
}

// Alterar senha do usuário
if (isset($_POST['change_password_id'])) {
    $user_id = intval($_POST['change_password_id']);
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    if ($new_password && $new_password === $confirm_password) {
        $hashed = password_hash($new_password, PASSWORD_DEFAULT);
        $sql_pass = "UPDATE usuario SET senha = ? WHERE id = ?";
        sqlsrv_query($conn, $sql_pass, [$hashed, $user_id]);
        echo "<script>alert('Senha alterada com sucesso!');</script>";
    } else {
        echo "<script>alert('As senhas não conferem!');</script>";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Usuários Cadastrados</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
<div class="container py-4">
  <h2 class="mb-3">Usuários</h2>
  <div class="table-responsive">
    <table class="table table-bordered table-striped">
      <thead class="table-dark">
        <tr>
          <th>ID</th>
          <th>Nome</th>
          <th>Email</th>
          <th>Cliente</th>
          <th>Admin</th>
          <th>Registrado em</th>
          <th>Status</th>
          <th>Ações</th>
        </tr>
      </thead>
      <tbody>
        <?php
        $sql = "SELECT u.id, u.nome, u.email, u.admin, u.criado_em, u.inativo, u.cliente_id, c.nome AS cliente_nome
                FROM usuario u
                LEFT JOIN cliente c ON u.cliente_id = c.id
                ORDER BY u.id DESC";
        $stmt = sqlsrv_query($conn, $sql);
        if ($stmt === false) {
            die("Error executing query: " . print_r(sqlsrv_errors(), true));
        }
        while ($u = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)):
        ?>
          <tr>
            <td><?=$u['id']?></td>
            <td><?=htmlspecialchars($u['nome'])?></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td><?=htmlspecialchars($u['cliente_nome'] ?? 'Não vinculado')?></td>
            <td>
              <?php if ($u['admin'] == 1): ?>
                <span class="badge bg-primary"><i class="bi bi-shield-lock"></i> Admin</span>
              <?php else: ?>
                <span class="badge bg-secondary"><i class="bi bi-person"></i> Usuário</span>
              <?php endif; ?>
            </td>
            <td><?=$u['criado_em']->format('d/m/Y H:i:s')?></td>
            <td>
              <?php if ($u['inativo'] == 1): ?>
                <span class="badge bg-danger">Inativo</span>
              <?php else: ?>
                <span class="badge bg-success">Ativo</span>
              <?php endif; ?>
            </td>
            <td>
              <?php if ($u['inativo'] == 1): ?>
                <a href="?toggle_id=<?=$u['id']?>" class="btn btn-sm btn-success">Reativar</a>
              <?php else: ?>
                <a href="?toggle_id=<?=$u['id']?>" class="btn btn-sm btn-warning">Desativar</a>
              <?php endif; ?>
              <a href="editar_usuario.php?id=<?=$u['id']?>" class="btn btn btn-info" title="Editar Usuário">
                <i class="bi bi-pencil-square"></i>
              </a>
              <button class="btn btn btn-primary" title="Alterar Senha" onclick="openPasswordModal(<?=$u['id']?>)">
                <i class="bi bi-key"></i>
              </button>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  </div>
  <div class="mt-3">
    <a href="registro.php" class="btn btn-success">Cadastrar Novo Usuário</a>
  </div>
</div>

<!-- Modal Alterar Senha -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="post" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="passwordModalLabel">Alterar Senha do Usuário</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="change_password_id" id="change_password_id">
        <div class="mb-3">
          <label for="new_password" class="form-label">Nova Senha</label>
          <input type="password" class="form-control" name="new_password" id="new_password" required>
        </div>
        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirme a Senha</label>
          <input type="password" class="form-control" name="confirm_password" id="confirm_password" required>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Alterar Senha</button>
      </div>
    </form>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
function openPasswordModal(userId) {
  document.getElementById('change_password_id').value = userId;
  var modal = new bootstrap.Modal(document.getElementById('passwordModal'));
  modal.show();
}
</script>
</body>
</html>