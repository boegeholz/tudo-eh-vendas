<?php
include_once 'auth_check.php';
include_once 'config.php';
include_once 'navbar.php';
if (!isset($_SESSION['cliente'])) {
    $_SESSION['cliente'] = 0;
}
if ($_SESSION['cliente'] > 0) {
  // Buscar procedimentos ativos
  $sql_lancamento = "select top 1 l.id, l.apurado_em from lancamento l
where l.cliente_id = ?
order by l.apurado_em desc, l.lancado_em desc";
  $stmt_proc = sqlsrv_query($conn, $sql_lancamento, [$_SESSION['cliente']]);
  $lancamento = sqlsrv_fetch_array($stmt_proc, SQLSRV_FETCH_ASSOC);

  $sql_proc = "SELECT p.id, p.descricao, ld.valor, ld.observacao 
FROM procedimento p, lancamento_detalhe ld WHERE ld.procedimento_id = p.id
and ld.lancamento_id = ?
ORDER BY p.id";
  $stmt_proc = sqlsrv_query($conn, $sql_proc, [$lancamento["id"]]);
  $procedimentos = [];
  while ($p = sqlsrv_fetch_array($stmt_proc, SQLSRV_FETCH_ASSOC)) {
    $procedimentos[] = $p;
  }

  $qtd_concluido = 0.0;
  $total_itens = 0;
  foreach ($procedimentos as $proc) {
    $total_itens++;
    if (isset($proc['valor'])) {
      if ($proc['valor'] === 1) {
        $qtd_concluido += 1.0;
      } elseif ($proc['valor'] === 2) {
        $qtd_concluido += 0.5;
      }
    }
  }
  $percentual_concluido = round($qtd_concluido * 100 / $total_itens, 2);
}


?>
<!DOCTYPE html>
<html>

<head>
  <meta charset="utf-8">
  <title>Dashboard • Tudo é vendas</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
  <div class="container py-5">
    <?php
    if ($_SESSION['cliente'] > 0) {
      ?>

      <h3 class="mb-4"><?php print_r($_SESSION['nome_cliente'] ?? "<Nenhum cliente selecionado>"); ?></h3>

      <a href="lancamento.php" class="btn btn-primary">
        Lançar Acompanhamento
      </a>
      <h4 class="mb-4">Progresso atual: <?= $percentual_concluido ?>%</h4>
      <div class="mb-3">
        <label class="form-label">Lançamento do dia <?= $lancamento['apurado_em']->format('d/m/Y') ?></label>
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
                  <td><?= htmlspecialchars($proc['descricao'], ENT_QUOTES, 'UTF-8') ?></td>
                  <td>
                    <div class="btn-group" role="group" aria-label="Preenchimento">
                      <input type="radio" class="btn-check" name="procedimento[<?= $proc['id'] ?>]"
                        id="sim<?= $proc['id'] ?>" value="1" autocomplete="off" <?php if (isset($proc['valor']) && $proc['valor'] === 1)
                            echo 'checked'; ?> disabled>
                      <label class="btn btn-outline-success" for="sim<?= $proc['id'] ?>">Sim</label>

                      <input type="radio" class="btn-check" name="procedimento[<?= $proc['id'] ?>]"
                        id="nao<?= $proc['id'] ?>" value="0" autocomplete="off" <?php if (isset($proc['valor']) && $proc['valor'] === 0)
                            echo 'checked'; ?> disabled>
                      <label class="btn btn-outline-danger" for="nao<?= $proc['id'] ?>">Não</label>

                      <input type="radio" class="btn-check" name="procedimento[<?= $proc['id'] ?>]"
                        id="asvezes<?= $proc['id'] ?>" value="2" autocomplete="off" <?php if (isset($proc['valor']) && $proc['valor'] === 2)
                            echo 'checked'; ?> disabled>
                      <label class="btn btn-outline-warning" for="asvezes<?= $proc['id'] ?>">Às vezes</label>
                    </div>
                  </td>
                  <td>
                    <input type="text" name="observacao[<?= $proc['id'] ?>]" class="form-control" maxlength="255"
                      placeholder="" value="<?= htmlspecialchars($proc['observacao'] ?? "", ENT_QUOTES, 'UTF-8') ?>"
                      disabled>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
      <?php
    } else {
      echo '<div class="alert alert-warning" role="alert">
      Nenhum cliente selecionado. Por favor, selecione um cliente para continuar.
    </div>';
    }
    ?>

  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>