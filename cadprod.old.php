<?php
session_start();

// Conexão com o banco de dados (substitua com suas credenciais)
$conexao = new mysqli("localhost", "seu_usuario", "sua_senha", "reconcavo");

// Verifica se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Inicializa variáveis para armazenar valores do formulário
$id = "";
$nome = "";
$descricao = "";
$precoCompra = "";
$margemLucro = "";
$precoVenda = "";
$quantidadeEstoque = "";

// Verifica se o formulário foi enviado (cadastro ou edição)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $nome = $_POST["nome"];
    $descricao = $_POST["descricao"];
    $precoCompra = $_POST["preco_compra"];
    $margemLucro = $_POST["margem_lucro"];
    $precoVenda = $_POST["preco_venda"];
    $quantidadeEstoque = $_POST["quantidade_em_estoque"];

    // Calcula o preço de venda com base na margem de lucro, se não estiver definido
    if (empty($precoVenda) && !empty($precoCompra) && !empty($margemLucro)) {
        $precoVenda = $precoCompra * (1 + ($margemLucro / 100));
    }

    // Calcula a margem de lucro com base no preço de compra, se não estiver definida
    if (empty($margemLucro) && !empty($precoCompra) && !empty($precoVenda)) {
        $margemLucro = (($precoVenda - $precoCompra) / $precoCompra) * 100;
    }

    // Verifica se é uma edição ou um novo cadastro
    if (empty($id)) {
        // Novo cadastro
        $sql = "INSERT INTO produtos (nome, descricao, preco_compra, margem_lucro, preco_venda, quantidade_em_estoque) VALUES ('$nome', '$descricao', $precoCompra, $margemLucro, $precoVenda, $quantidadeEstoque)";
    } else {
        // Edição
        $sql = "UPDATE produtos SET nome='$nome', descricao='$descricao', preco_compra=$precoCompra, margem_lucro=$margemLucro, preco_venda=$precoVenda, quantidade_em_estoque=$quantidadeEstoque WHERE id=$id";
    }

    if ($conexao->query($sql) === TRUE) {
        $_SESSION["mensagem"] = "Produto salvo com sucesso!";
        header("Location: lista_produtos.php"); // Redireciona para a lista de produtos
    } else {
        $_SESSION["mensagem"] = "Erro ao salvar o produto: " . $conexao->error;
    }
}

// Função para carregar os dados do produto para edição (se houver um ID)
function carregarProdutoParaEdicao($conexao, $id) {
    $sql = "SELECT * FROM produtos WHERE id=$id";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows == 1) {
        $row = $resultado->fetch_assoc();
        return $row;
    } else {
        return null;
    }
}

// Verifica se é uma edição (o ID do produto foi passado via GET)
if (isset($_GET["id"])) {
    $id = $_GET["id"];
    $produtoParaEditar = carregarProdutoParaEdicao($conexao, $id);

    if ($produtoParaEditar) {
        $nome = $produtoParaEditar["nome"];
        $descricao = $produtoParaEditar["descricao"];
        $precoCompra = $produtoParaEditar["preco_compra"];
        $margemLucro = $produtoParaEditar["margem_lucro"];
        $precoVenda = $produtoParaEditar["preco_venda"];
        $quantidadeEstoque = $produtoParaEditar["quantidade_em_estoque"];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Produto</title>
</head>
<body>
    <h2>Cadastro de Produto</h2>
    <?php
    if (isset($_SESSION["mensagem"])) {
        echo "<p>" . $_SESSION["mensagem"] . "</p>";
        unset($_SESSION["mensagem"]);
    }
    ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <!-- Campo oculto para o ID do produto (usado na edição) -->
        <input type="hidden" name="id" value="<?php echo $id; ?>">

        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" value="<?php echo $nome; ?>" required><br><br>

        <label for="descricao">Descrição:</label>
        <textarea id="descricao" name="descricao"><?php echo $descricao; ?></textarea><br><br>

        <label for="preco_compra">Preço de Compra:</label>
        <input type="number" id="preco_compra" name="preco_compra" value="<?php echo $precoCompra; ?>" step="0.01" required><br><br>

        <label for="margem_lucro">Margem de Lucro (%):</label>
        <input type="number" id="margem_lucro" name="margem_lucro" value="<?php echo $margemLucro; ?>" step="0.01"><br><br>

        <label for="preco_venda">Preço de Venda:</label>
        <input type="number" id="preco_venda" name="preco_venda" value="<?php echo $precoVenda; ?>" step="0.01"><br><br>

        <label for="quantidade_em_estoque">Quantidade em Estoque:</label>
        <input type="number" id="quantidade_em_estoque" name="quantidade_em_estoque" value="<?php echo $quantidadeEstoque; ?>" required><br><br>

        <input type="submit" value="Salvar">
    </form>
</body>
</html>
