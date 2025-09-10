<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';

// Ativar/desativar cliente
if (isset($_GET['toggle_id'])) {
  $toggle_id = intval($_GET['toggle_id']);
  $sql_check = "SELECT inativo FROM cliente WHERE id = ?";
  $stmt_check = sqlsrv_query($conn, $sql_check, [$toggle_id]);
  $row = sqlsrv_fetch_array($stmt_check, SQLSRV_FETCH_ASSOC);
  if ($row) {
    $novo_status = ($row['inativo'] == 1) ? 0 : 1;
    $sql_toggle = "UPDATE cliente SET inativo = ? WHERE id = ?";
    sqlsrv_query($conn, $sql_toggle, [$novo_status, $toggle_id]);
    header("Location: lista_clientes.php");
    exit;
  }
}

// Inserir novo cliente
if (isset($_POST['novo_nome'])) {
  $nome = $_POST['novo_nome'];
  $sql = "INSERT INTO cliente (nome) VALUES (?)";
  sqlsrv_query($conn, $sql, [$nome]);
  header('Location: lista_clientes.php');
  exit;
}

// Consulta clientes
$sql = "SELECT id, nome, inativo FROM cliente ORDER BY id";
$stmt = sqlsrv_query($conn, $sql);
?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Lista de clientes</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>

<body>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="mb-0">Clientes</h2>
      <button class="btn btn-success" onclick="openNovoclienteModal()">
        <i class="bi bi-plus-lg"></i> Novo cliente
      </button>
    </div>
    <div class="table-responsive">
      <table class="table table-bordered table-striped">
        <thead class="table-dark">
          <tr>
            <th>ID</th>
            <th>Descrição</th>
            <th>Status</th>
            <th>Ações</th>
          </tr>
        </thead>
        <tbody>
          <?php while ($p = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)): ?>
            <tr>
              <td><?= $p['id'] ?></td>
              <td><?= htmlspecialchars($p['nome'], ENT_QUOTES, 'UTF-8') ?></td>
              <td>
                <?php if ($p['inativo'] == 1): ?>
                  <span class="badge bg-danger">Inativo</span>
                <?php else: ?>
                  <span class="badge bg-success">Ativo</span>
                <?php endif; ?>
              </td>
              <td>
                <?php if ($p['inativo'] == 1): ?>
                  <a href="?toggle_id=<?= $p['id'] ?>" class="btn btn-sm btn-success">Ativar</a>
                <?php else: ?>
                  <a href="?toggle_id=<?= $p['id'] ?>" class="btn btn-sm btn-warning">Desativar</a>
                <?php endif; ?>
                <a href="editar_cliente.php?id=<?= $p['id'] ?>" class="btn btn-info" title="Editar cliente">
                  <i class="bi bi-pencil-square"></i>
                </a>
              </td>
            </tr>
          <?php endwhile; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Novo cliente -->
  <div class="modal fade" id="novoclienteModal" tabindex="-1" aria-labelledby="novoclienteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <form method="post" class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="novoclienteModalLabel">Novo cliente</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="novo_nome" class="form-label">Nome</label>
            <textarea name="novo_nome" id="novo_nome" class="form-control" required></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
          <button type="submit" class="btn btn-success">Adicionar</button>
        </div>
      </form>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function openNovoclienteModal() {
      var modal = new bootstrap.Modal(document.getElementById('novoclienteModal'));
      modal.show();
    }
  </script>
</body>

</html>