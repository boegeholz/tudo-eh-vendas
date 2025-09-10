<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once 'config.php';

// Buscar clientes para o listbox
$sql_cli = "SELECT id, nome FROM cliente ORDER BY nome";
$stmt_cli = sqlsrv_query($conn, $sql_cli);
?>
<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
  <div class="container-fluid">
    <a class="navbar-brand" href="dashboard.php">
        <img src="images/logo.png" alt="Logo" style="height:32px; vertical-align:middle; margin-right:8px;">
        Dashboard
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarsExample" aria-controls="navbarsExample"
            aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarsExample">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">
        
        <?php if (!empty($_SESSION['admin']) && $_SESSION['admin'] === 1): ?>
          <li class="nav-item"><a class="nav-link" href="lista_clientes.php">[adm] Clientes</a></li>
          <li class="nav-item"><a class="nav-link" href="lista_procedimentos.php">[adm] Procedimentos</a></li>
          <li class="nav-item"><a class="nav-link" href="lista_usuarios.php">[adm] Usuários</a></li>
      </ul>
      <form method="post" class="d-flex flex-column align-items-start me-3">
        <select name="cliente_id" class="form-select form-select-sm mb-2" onchange="updateClienteNome(this)">
          <option value="">Selecione o cliente</option>
          <?php while ($cli = sqlsrv_fetch_array($stmt_cli, SQLSRV_FETCH_ASSOC)): ?>
            <option value="<?=$cli['id']?>" data-nome="<?=htmlspecialchars($cli['nome'], ENT_QUOTES, 'UTF-8')?>"
              <?=(!empty($_SESSION['cliente']) && $_SESSION['cliente'] == $cli['id']) ? 'selected' : ''?>>
              <?=htmlspecialchars($cli['nome'], ENT_QUOTES, 'UTF-8')?>
            </option>
          <?php endwhile; ?>
        </select>
        <input type="hidden" name="cliente_nome" id="cliente_nome_hidden" value="">
        <button type="submit" name="visualizar_cliente" class="btn btn-sm btn-outline-light">Visualizar</button>
      </form>
        <?php endif; ?>

      
      <span class="navbar-text text-white me-3" style="font-size: 0.85rem;">
        <?=htmlspecialchars($_SESSION['email'])?><br>
        <?=htmlspecialchars($_SESSION['nome_cliente']??"<Nenhum cliente selecionado>")?>
      </span>
      <a class="btn btn-outline-light btn-sm" href="logout.php">Logout</a>
    </div>
  </div>
</nav>
<?php
// Armazena o cliente selecionado na sessão
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['visualizar_cliente'])) {
    $_SESSION['cliente'] = $_POST['cliente_id'];
    $_SESSION['nome_cliente'] = $_POST['cliente_nome'];
    header("Location: " . $_SERVER['REQUEST_URI']);
    exit;
}
?>
<script>
function updateClienteNome(select) {
  var nome = select.options[select.selectedIndex].getAttribute('data-nome') || '';
  document.getElementById('cliente_nome_hidden').value = nome;
}
// Atualiza ao carregar se já houver cliente selecionado
document.addEventListener('DOMContentLoaded', function() {
  var select = document.querySelector('select[name="cliente_id"]');
  if (select) updateClienteNome(select);
});
</script>