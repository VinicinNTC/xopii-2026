<?php
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Quem Somos? Xopii</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light min-vh-100 d-flex flex-column">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Xopii</a>
            
            <div class="d-flex align-items-center">
                <span class="text-light me-3">Olá <strong><?php echo $_SESSION['usuario_nome']; ?></strong></span>
                <a href="carrinho.php" class="btn btn-outline-light btn-sm me-2">🛒 Carrinho</a>
                <a href="estoque.php" class="btn btn-outline-light btn-sm me-2">📦 Estoque</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container my-5 flex-grow-1">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-dark">Quem Somos?</h2>
                <p class="text-muted">Conheça mais sobre a nossa história e missão.</p>
            </div>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-4">
                <h4 class="fw-bold text-dark mb-3">Bem-vindo à Xopii Electronics</h4>
                <p class="text-secondary">
                    A <strong>Xopii</strong> nasceu com o propósito de trazer a você às melhores tecnologias e produtos eletrônicos do mercado com praticidade, segurança e preços justos. 
                </p>
                <p class="text-secondary">
                    Nosso compromisso é oferecer uma experiência de compra simples e rápida, garantindo qualidade em cada item disponível em nosso estoque. Seja para renovar seus gadgets ou encontrar o eletrônico perfeito, estamos aqui para facilitar o seu dia a dia. Encontre seus produtos desejados na pagina principal, e aproveite nosso site!
                </p>
            </div>
        </div>
    </div>

    <footer class="text-center text-white py-3 border-top bg-dark mt-auto">
        Xopii Eletronics ™ <br>
        made by Vinicius / Lucas
    </footer>

</body>
</html>