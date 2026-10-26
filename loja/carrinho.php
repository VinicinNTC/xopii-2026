<?php
include 'conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

// Remover item do carrinho (1 a 1 ou excluir)
if (isset($_GET['remover'])) {
    $id_remover = intval($_GET['remover']);
    if (isset($_SESSION['carrinho'][$id_remover])) {
        $_SESSION['carrinho'][$id_remover]--; 
        
        if ($_SESSION['carrinho'][$id_remover] <= 0) {
            unset($_SESSION['carrinho'][$id_remover]);
        }
    }
    header("Location: carrinho.php");
    exit;
}

// Finalizar compra (Baixa no estoque)
if (isset($_GET['finalizar']) && !empty($_SESSION['carrinho'])) {
    $conn->query("START TRANSACTION");
    $sucesso_transacao = true;

    foreach ($_SESSION['carrinho'] as $id_produto => $qtd) {
        $sql_baixa = "UPDATE produtos SET estoque = estoque - $qtd WHERE id = $id_produto";
        if (!$conn->query($sql_baixa)) {
            $sucesso_transacao = false;
            break;
        }
    }

    if ($sucesso_transacao) {
        $conn->query("COMMIT");
        unset($_SESSION['carrinho']);
        $sucesso = "Compra realizada com sucesso! Estoque atualizado no sistema.";
    } else {
        $conn->query("ROLLBACK");
        $erro = "Erro ao finalizar compra.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Carrinho de Compras</title>
    <!-- Importando o Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- navbar goat -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php"> Xopii </a>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Olá <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">🛍️ Vitrine</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <!-- main se -->
    <div class="container my-5">
        
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-dark">Seu Carrinho de Compras</h2>
                <p class="text-muted">Revise seus itens antes de finalizar o pedido.</p>
            </div>
        </div>

        <?php if(isset($sucesso)) { ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?php echo $sucesso; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if(isset($erro)) { ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?php echo $erro; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php } ?>

        <?php if (empty($_SESSION['carrinho'])) { ?>
            <div class="card shadow-sm border-0 text-center py-5">
                <div class="card-body">
                    <h4 class="text-muted mb-3">Seu carrinho está vazio 🛒</h4>
                    <p class="text-muted mb-4">Você ainda não adicionou nenhum produto.</p>
                    <a href="index.php" class="btn btn-primary px-4">Voltar para a Vitrine</a>
                </div>
            </div>
        <?php } else { ?>

            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th class="py-3 ps-4">Produto</th>
                                    <th class="py-3">Quantidade</th>
                                    <th class="py-3">Preço Unitário</th>
                                    <th class="py-3">Subtotal</th>
                                    <th class="py-3 text-center">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $total_geral = 0;
                                foreach ($_SESSION['carrinho'] as $id_produto => $qtd) {
                                    $res = $conn->query("SELECT * FROM produtos WHERE id = $id_produto");
                                    $prod = $res->fetch_assoc();
                                    $subtotal = $prod['preco'] * $qtd;
                                    $total_geral += $subtotal;
                                ?>
                                <tr>
                                    <td class="ps-4 fw-semibold"><?php echo $prod['nome']; ?></td>
                                    <td><span class="badge bg-secondary px-2 py-1"><?php echo $qtd; ?> un.</span></td>
                                    <td class="text-muted">R$ <?php echo number_format($prod['preco'], 2, ',', '.'); ?></td>
                                    <td class="text-success fw-bold">R$ <?php echo number_format($subtotal, 2, ',', '.'); ?></td>
                                    <td class="text-center">
                                        <a href="carrinho.php?remover=<?php echo $id_produto; ?>" class="btn btn-outline-danger btn-sm px-3">Remover 1</a>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

           
            <div class="row justify-content-end">
                <div class="col-md-5">
                    <div class="card shadow-sm border-0 bg-white p-4">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="h5 text-muted mb-0">Total Geral:</span>
                            <span class="h4 text-success fw-bold mb-0">R$ <?php echo number_format($total_geral, 2, ',', '.'); ?></span>
                        </div>
                        <hr>
                        <div class="d-grid gap-2">
                            <a href="carrinho.php?finalizar=1" class="btn btn-success btn-lg">Finalizar Compra</a>
                            <a href="index.php" class="btn btn-outline-secondary">Continuar Comprando</a>
                        </div>
                    </div>
                </div>
            </div>

        <?php } ?>

    </div>

</body>
</html>