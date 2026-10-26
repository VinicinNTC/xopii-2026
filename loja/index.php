<?php
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Adicionar ao carrinho
if (isset($_GET['comprar'])) {
    $id_produto = intval($_GET['comprar']);
    
    // Verifica estoque antes de adicionar
    $sql_est = "SELECT estoque FROM produtos WHERE id = $id_produto";
    $res_est = $conn->query($sql_est);
    $prod = $res_est->fetch_assoc();

    if ($prod['estoque'] > 0) {
        if (!isset($_SESSION['carrinho'])) {
            $_SESSION['carrinho'] = array();
        }
        
        // Adiciona ou incrementa no carrinho da sessão
        if (isset($_SESSION['carrinho'][$id_produto])) {
            $_SESSION['carrinho'][$id_produto]++;
        } else {
            $_SESSION['carrinho'][$id_produto] = 1;
        }
        header("Location: index.php");
        exit;
    } else {
        $erro = "Produto esgotado!";
    }
}

$resultado = $conn->query("SELECT * FROM produtos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Loja de Eletrônicos</title>
    <!-- Importando o Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- navbar pq eh mt bom -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"> Xopii</a>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Olá <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
                <a href="carrinho.php" class="btn btn-outline-light btn-sm me-2">🛒 Carrinho</a>
                <a href="estoque.php" class="btn btn-outline-light btn-sm me-2">📦 Estoque</a>
                <a href="quemsomos.php" class="btn btn-outline-light btn-sm me-2"> Quem Somos?</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-dark">Produtos Eletrônicos</h2>
                <p class="text-muted">Confira nossos produtos disponíveis em estoque.</p>
            </div>
        </div>

        <?php if(isset($erro)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo $erro; ?></div>
        <?php } ?>

        <!-- Tabela de Produtos Estilizada -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
    <thead class="table-dark">
        <tr>
            <th class="py-3 ps-4">Produto</th>
            <th class="py-3">Descrição</th>
            <th class="py-3">Preço</th>
            <th class="py-3">Estoque Disponível</th>
            <th class="py-3 text-center">Ação</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        while($row = $resultado->fetch_assoc()) { 
            $id_prod = $row['id'];
            
            // Verifica se o produto já está no carrinho da sessão e pega a quantidade
            $qtd_no_carrinho = isset($_SESSION['carrinho'][$id_prod]) ? $_SESSION['carrinho'][$id_prod] : 0;
            
            // Calcula o estoque visual (Estoque real do banco - o que está no carrinho)
            $estoque_visual = $row['estoque'] - $qtd_no_carrinho;
        ?>
        <tr>
            <td class="ps-4 fw-semibold"><?php echo $row['nome']; ?></td>
            <td class="text-muted"><?php echo $row['descricao']; ?></td>
            <td class="text-success fw-bold">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
            <td>
                <?php if ($estoque_visual > 0) { ?>
                    <span class="badge bg-success"><?php echo $estoque_visual; ?> un.</span>
                <?php } else { ?>
                    <span class="badge bg-danger">Esgotado</span>
                <?php } ?>
            </td>
            <td class="text-center">
                <?php if ($estoque_visual > 0) { ?>
                    <a href="index.php?comprar=<?php echo $row['id']; ?>" class="btn btn-primary btn-sm px-3" onclick="alert('Produto Adicionado ao Carrinho!');">Adicionar ao Carrinho</a>
                <?php } else { ?>
                    <button class="btn btn-secondary btn-sm px-3" disabled>Indisponível</button>
                <?php } ?>
            </td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
                </div>
            </div>
        </div>

    </div>


    <footer class="text-center text-muted my-4 py-3 border-top">
        Xopii Eletronics ™ 
    </footer>
</body>
</html>