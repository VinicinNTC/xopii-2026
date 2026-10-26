<?php
include 'conexao.php';

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

$mensagem = "";
$erro = "";

// Ação para Cadastrar ou Atualizar Produto
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['acao'])) {
        $nome = trim($_POST['nome']);
        $descricao = trim($_POST['descricao']);
        $preco = floatval(str_replace(',', '.', $_POST['preco']));
        $estoque = intval($_POST['estoque']);

        if ($_POST['acao'] == 'cadastrar') {
            if (!empty($nome) && $preco >= 0 && $estoque >= 0) {
                $sql = "INSERT INTO produtos (nome, descricao, preco, estoque) VALUES ('$nome', '$descricao', $preco, $estoque)";
                if ($conn->query($sql)) {
                    $mensagem = "Produto cadastrado com sucesso!";
                } else {
                    $erro = "Erro ao cadastrar produto: " . $conn->error;
                }
            } else {
                $erro = "Preencha os campos corretamente.";
            }
        } elseif ($_POST['acao'] == 'atualizar') {
            $id = intval($_POST['id']);
            if ($id > 0 && !empty($nome) && $preco >= 0 && $estoque >= 0) {
                $sql = "UPDATE produtos SET nome = '$nome', descricao = '$descricao', preco = $preco, estoque = $estoque WHERE id = $id";
                if ($conn->query($sql)) {
                    $mensagem = "Produto atualizado com sucesso!";
                } else {
                    $erro = "Erro ao atualizar produto: " . $conn->error;
                }
            } else {
                $erro = "Dados inválidos para atualização.";
            }
        }
    }
}

// Ação para Excluir Produto
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    $sql = "DELETE FROM produtos WHERE id = $id";
    if ($conn->query($sql)) {
        header("Location: estoque.php");
        exit;
    } else {
        $erro = "Erro ao excluir produto.";
    }
}

// Busca produto para edição se solicitado
$produto_edicao = null;
if (isset($_GET['editar'])) {
    $id_edicao = intval($_GET['editar']);
    $res_edicao = $conn->query("SELECT * FROM produtos WHERE id = $id_edicao");
    if ($res_edicao->num_rows > 0) {
        $produto_edicao = $res_edicao->fetch_assoc();
    }
}

// Lista todos os produtos
$resultado = $conn->query("SELECT * FROM produtos");
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Gerenciamento de Estoque - Xopii</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold" href="index.php">Xopii</a>
            <div class="d-flex align-items-center">
                <a href="index.php" class="btn btn-outline-light btn-sm me-2">🏠 Voltar à Loja</a>
                <a href="logout.php" class="btn btn-danger btn-sm">Sair</a>
            </div>
        </div>
    </nav>

    <div class="container my-5">
        <div class="row mb-4">
            <div class="col">
                <h2 class="fw-bold text-dark">Gerenciamento de Estoque</h2>
                <p class="text-muted">Adicione, edite ou remova produtos do sistema.</p>
            </div>
        </div>

        <?php if(!empty($mensagem)) { ?>
            <div class="alert alert-success" role="alert"><?php echo $mensagem; ?></div>
        <?php } ?>

        <?php if(!empty($erro)) { ?>
            <div class="alert alert-danger" role="alert"><?php echo $erro; ?></div>
        <?php } ?>

        <!-- Formulário de Cadastro / Edição -->
        <div class="card shadow-sm border-0 mb-5">
            <div class="card-body">
                <h4 class="card-title mb-4"><?php echo $produto_edicao ? 'Editar Produto' : 'Cadastrar Novo Produto'; ?></h4>
                <form action="estoque.php" method="POST">
                    <input type="hidden" name="acao" value="<?php echo $produto_edicao ? 'atualizar' : 'cadastrar'; ?>">
                    <?php if ($produto_edicao) { ?>
                        <input type="hidden" name="id" value="<?php echo $produto_edicao['id']; ?>">
                    <?php } ?>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="nome" class="form-label">Nome do Produto</label>
                            <input type="text" class="form-control" id="nome" name="nome" value="<?php echo $produto_edicao ? $produto_edicao['nome'] : ''; ?>" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="preco" class="form-label">Preço (R$)</label>
                            <input type="text" class="form-control" id="preco" name="preco" value="<?php echo $produto_edicao ? $produto_edicao['preco'] : ''; ?>" placeholder="0.00" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="estoque" class="form-label">Quantidade em Estoque</label>
                            <input type="number" class="form-control" id="estoque" name="estoque" value="<?php echo $produto_edicao ? $produto_edicao['estoque'] : ''; ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="2"><?php echo $produto_edicao ? $produto_edicao['descricao'] : ''; ?></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-success px-4"><?php echo $produto_edicao ? 'Salvar Alterações' : 'Cadastrar Produto'; ?></button>
                    <?php if ($produto_edicao) { ?>
                        <a href="estoque.php" class="btn btn-secondary px-3">Cancelar</a>
                    <?php } ?>
                </form>
            </div>
        </div>

        <!-- Tabela de Produtos Cadastrados -->
        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th class="py-3 ps-4">ID</th>
                                <th class="py-3">Produto</th>
                                <th class="py-3">Descrição</th>
                                <th class="py-3">Preço</th>
                                <th class="py-3">Estoque</th>
                                <th class="py-3 text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $resultado->fetch_assoc()) { ?>
                            <tr>
                                <td class="ps-4 text-muted">#<?php echo $row['id']; ?></td>
                                <td class="fw-semibold"><?php echo $row['nome']; ?></td>
                                <td class="text-muted"><?php echo $row['descricao']; ?></td>
                                <td class="text-success fw-bold">R$ <?php echo number_format($row['preco'], 2, ',', '.'); ?></td>
                                <td><span class="badge bg-secondary"><?php echo $row['estoque']; ?> un.</span></td>
                                <td class="text-center">
                                    <a href="estoque.php?editar=<?php echo $row['id']; ?>" class="btn btn-warning btn-sm px-2">Editar</a>
                                    <a href="estoque.php?excluir=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm px-2" onclick="return confirm('Deseja realmente excluir este produto?')">Excluir</a>
                                </td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

</body>
</html>