<?php
require 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: index.php'); exit; }

$nome     = trim($_POST['nome'] ?? '');
$email    = trim($_POST['email'] ?? '');
$telefone = trim($_POST['telefone'] ?? '');
$endereco = trim($_POST['endereco'] ?? '');
$qtd      = $_POST['qtd'] ?? [];

$erro = null;
if ($nome === '' || $telefone === '' || $endereco === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erro = 'Preencha todos os campos corretamente.';
}

$itens = [];
$total = 0;
if (!$erro) {
    // Preço sempre vem do banco, nunca do formulário
    $stmt = $pdo->prepare("SELECT id, nome, preco FROM produtos WHERE id = ? AND ativo = 1");
    foreach ($qtd as $id => $q) {
        $q = (int)$q;
        if ($q < 1 || $q > 99) continue;
        $stmt->execute([(int)$id]);
        if ($p = $stmt->fetch()) {
            $p['quantidade'] = $q;
            $itens[] = $p;
            $total += $p['preco'] * $q;
        }
    }
    if (!$itens) $erro = 'Nenhum produto selecionado.';
}

$pedidoId = null;
if (!$erro) {
    try {
        $pdo->beginTransaction();
        $pdo->prepare("INSERT INTO pedidos (cliente_nome, cliente_email, cliente_telefone, endereco, total)
                       VALUES (?, ?, ?, ?, ?)")
            ->execute([$nome, $email, $telefone, $endereco, $total]);
        $pedidoId = $pdo->lastInsertId();

        $ins = $pdo->prepare("INSERT INTO itens_pedido (pedido_id, produto_id, quantidade, preco_unitario)
                              VALUES (?, ?, ?, ?)");
        foreach ($itens as $i) {
            $ins->execute([$pedidoId, $i['id'], $i['quantidade'], $i['preco']]);
        }
        $pdo->commit();
    } catch (Exception $e) {
        $pdo->rollBack();
        $erro = 'Não foi possível salvar o pedido. Tente novamente.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Pedido</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<main class="resumo">
  <?php if ($erro): ?>
    <h2>Ops!</h2>
    <p><?= h($erro) ?></p>
    <a href="index.php" class="btn">Voltar</a>
  <?php else: ?>
    <h2>Pedido #<?= (int)$pedidoId ?> recebido! 🎉</h2>
    <p>Obrigado, <?= h($nome) ?>. Entraremos em contato pelo telefone <?= h($telefone) ?>.</p>
    <table>
      <tr><th>Produto</th><th>Qtd</th><th>Subtotal</th></tr>
      <?php foreach ($itens as $i): ?>
        <tr>
          <td><?= h($i['nome']) ?></td>
          <td><?= (int)$i['quantidade'] ?></td>
          <td><?= moeda($i['preco'] * $i['quantidade']) ?></td>
        </tr>
      <?php endforeach; ?>
      <tr class="total-linha"><td colspan="2">Total</td><td><?= moeda($total) ?></td></tr>
    </table>
    <a href="index.php" class="btn">Fazer novo pedido</a>
  <?php endif; ?>
</main>
</body>
</html>
