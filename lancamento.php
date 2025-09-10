<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';

// Salvar lançamento e detalhes
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['data_lancamento'])) {
    $data_lancamento = $_POST['data_lancamento'];
    $observacao_lancamento = $_POST['observacao_lancamento'] ?? '';
    $cliente_id = $_SESSION['cliente'] ?? null;

    // Insere lançamento principal e obtém o ID
    $sql_lanc = "INSERT INTO lancamento (sequencia, apurado_em, cliente_id, observacao) VALUES ((select coalesce(max(sequencia), 0) + 1 as sequencia from lancamento
where cliente_id = 1), ?, ?, ?); SELECT @@IDENTITY as id;";
    $stmt_lanc = sqlsrv_query($conn, $sql_lanc, [$data_lancamento, $cliente_id, $observacao_lancamento]);
    $next_result = sqlsrv_next_result($stmt_lanc);
    $row = sqlsrv_fetch_array($stmt_lanc);
    $lancamento_id = $row['id'];  // Use este ID para o próximo

    // Insere detalhes
    if (!empty($_POST['procedimento'])) {
        foreach ($_POST['procedimento'] as $proc_id => $valor) {
            $observacao = $_POST['observacao'][$proc_id] ?? '';
            $sql_det = "INSERT INTO lancamento_detalhe (lancamento_id, procedimento_id, valor, observacao) VALUES (?, ?, ?, ?)";
            sqlsrv_query($conn, $sql_det, [$lancamento_id, $proc_id, $valor, $observacao]);
        }
    }
    header('Location: dashboard.php');
    exit;
}

// Buscar procedimentos ativos
$sql_proc = "SELECT id, descricao FROM procedimento WHERE inativo = 0 ORDER BY id";
$stmt_proc = sqlsrv_query($conn, $sql_proc);
$procedimentos = [];
while ($p = sqlsrv_fetch_array($stmt_proc, SQLSRV_FETCH_ASSOC)) {
    $procedimentos[] = $p;
}
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>Lançar Acompanhamento</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-4">
  <h2 class="mb-3">Lançar Acompanhamento</h2>
  <form method="post" class="mb-4" id="formLancamento">
    <div class="mb-3">
      <label for="data_lancamento" class="form-label">Data do acompanhamento</label>
      <input type="date" name="data_lancamento" id="data_lancamento" class="form-control" required>
      <label for="observacao_lancamento" class="form-label">Observação do lançamento (opcional)</label>
      <input type="text" name="observacao_lancamento" id="observacao_lancamento" class="form-control" maxlength="255" placeholder="Observação do lançamento">
    </div>
    <div class="mb-3">
      <label class="form-label">Procedimentos</label>
      <div class="table-responsive">
        <table class="table table-bordered table-striped">
          <thead class="table-dark">
            <tr>
              <th>Procedimento</th>
              <th>Preenchimento</th>
              <th>Observação</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($procedimentos as $proc): ?>
              <tr>
                <td><?=htmlspecialchars($proc['descricao'], ENT_QUOTES, 'UTF-8')?></td>
                <td>
                  <div class="btn-group" role="group" aria-label="Preenchimento">
                    <input type="radio" class="btn-check" name="procedimento[<?=$proc['id']?>]" id="sim<?=$proc['id']?>" value="1" autocomplete="off">
                    <label class="btn btn-outline-success" for="sim<?=$proc['id']?>">Sim</label>

                    <input type="radio" class="btn-check" name="procedimento[<?=$proc['id']?>]" id="nao<?=$proc['id']?>" value="0" autocomplete="off">
                    <label class="btn btn-outline-danger" for="nao<?=$proc['id']?>">Não</label>

                    <input type="radio" class="btn-check" name="procedimento[<?=$proc['id']?>]" id="asvezes<?=$proc['id']?>" value="2" autocomplete="off">
                    <label class="btn btn-outline-warning" for="asvezes<?=$proc['id']?>">Às vezes</label>
                  </div>
                </td>
                <td>
                  <input type="text" name="observacao[<?=$proc['id']?>]" class="form-control" maxlength="255" placeholder="Observação">
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
    <button type="submit" class="btn btn-success" id="btnSalvar">Salvar</button>
    <a href="dashboard.php" class="btn btn-secondary">Cancelar</a>
  </form>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.getElementById('formLancamento').addEventListener('submit', function(e) {
  let allFilled = true;
  const rows = document.querySelectorAll('tbody tr');
  rows.forEach(row => {
    const radios = row.querySelectorAll('input[type="radio"]');
    let checked = false;
    radios.forEach(radio => {
      if (radio.checked && (radio.value === "0" || radio.value === "1" || radio.value === "2")) {
        checked = true;
      }
    });
    if (!checked) allFilled = false;
  });
  if (!allFilled) {
    e.preventDefault();
    alert('Preencha todos os procedimentos antes de salvar!');
  }
});
</script>
</body>
</html>