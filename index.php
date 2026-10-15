<?php
require 'config.php';
$produtos = $pdo->query("SELECT * FROM produtos WHERE ativo = 1 ORDER BY codigo")->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Loja de Sapatos</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<header class="hero">
  <h1>Sapatos para todos os estilos</h1>
  <p>Escolha seus favoritos e faça o pedido em poucos cliques.</p>
  <a href="#produtos" class="btn">Ver produtos</a>
</header>

<main>
  <form action="pedido.php" method="post" id="form-pedido">
    <section id="produtos">
      <h2>Nossos produtos</h2>
      <div class="grid">
        <?php foreach ($produtos as $p): ?>
          <article class="card">
            <div class="foto" style="background: <?= h($p['cor']) ?>">
              <span><?= h($p['codigo']) ?></span>
            </div>
            <h3><?= h($p['nome']) ?></h3>
            <p class="desc"><?= h($p['descricao']) ?></p>
            <p class="preco"><?= moeda($p['preco']) ?></p>
            <label>Quantidade
              <input type="number" name="qtd[<?= (int)$p['id'] ?>]" value="0" min="0" max="99"
                     data-preco="<?= h($p['preco']) ?>" class="qtd">
            </label>
          </article>
        <?php endforeach; ?>
      </div>
      <p class="total">Total: <strong id="total">R$ 0,00</strong></p>
    </section>

    <section id="dados">
      <h2>Seus dados</h2>
      <div class="campos">
        <input type="text" name="nome" placeholder="Nome completo" required maxlength="120">
        <input type="email" name="email" placeholder="E-mail" required maxlength="120">
        <input type="tel" name="telefone" placeholder="Telefone / WhatsApp" required maxlength="30">
        <input type="text" name="endereco" placeholder="Endereço de entrega" required maxlength="255">
      </div>
      <button type="submit" class="btn">Finalizar pedido</button>
    </section>
  </form>
</main>

<footer><p>&copy; <?= date('Y') ?> Loja de Sapatos</p></footer>

<script>
  const qtds = document.querySelectorAll('.qtd');
  const totalEl = document.getElementById('total');
  function atualizar() {
    let total = 0;
    qtds.forEach(i => total += (parseInt(i.value) || 0) * parseFloat(i.dataset.preco));
    totalEl.textContent = total.toLocaleString('pt-BR', {style: 'currency', currency: 'BRL'});
  }
  qtds.forEach(i => i.addEventListener('input', atualizar));

  document.getElementById('form-pedido').addEventListener('submit', e => {
    let soma = 0;
    qtds.forEach(i => soma += parseInt(i.value) || 0);
    if (soma === 0) { e.preventDefault(); alert('Escolha pelo menos um produto.'); }
  });
</script>
</body>
</html>
