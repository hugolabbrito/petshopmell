<?php
session_start();

// Conexão ao banco de dados (substitua com suas próprias credenciais)
$conexao = new mysqli("localhost", "hugo", "leokgb", "petshopmell");

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Variáveis de edição
$edicao_id = "";
$edicao_nome = "";
$edicao_descricao = "";
$edicao_valor_compra = "";
$edicao_porcentagem_lucro = "";
$edicao_valor_venda = "";
$edicao_quantidade_em_estoque = "";

// Verifique se um ID de produto foi fornecido na URL
if (isset($_GET["id"])) {
    $id = $_GET["id"];

    // Consulta SQL para obter os detalhes do produto com base no ID
    $sql_detalhes = "SELECT * FROM produtos WHERE id = $id";
    $resultado_detalhes = $conexao->query($sql_detalhes);

    if ($resultado_detalhes->num_rows > 0) {
        $row = $resultado_detalhes->fetch_assoc();
        $edicao_id = $row["id"];
        $edicao_nome = $row["nome"];
        $edicao_descricao = $row["descricao"];
        $edicao_valor_compra = $row["valor_compra"];
        $edicao_porcentagem_lucro = $row["porcentagem_lucro"];
        $edicao_valor_venda = $row["valor_venda"];
        $edicao_quantidade_em_estoque = $row["quantidade_em_estoque"];
    } else {
        echo "Produto não encontrado.";
        exit;
    }
}

// Verifique se o formulário foi enviado para edição
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["editar"])) {
    $edicao_id = $_POST["edicao_id"];
    $edicao_nome = $_POST["edicao_nome"];
    $edicao_descricao = $_POST["edicao_descricao"];
    $edicao_valor_compra = $_POST["edicao_valor_compra"];
    $edicao_valor_venda = $_POST["edicao_valor_venda"];
    $edicao_quantidade_em_estoque = $_POST["edicao_quantidade_em_estoque"];

    // Calcular a porcentagem de lucro
    $edicao_porcentagem_lucro = (($edicao_valor_venda - $edicao_valor_compra) / $edicao_valor_compra) * 100;

    // Consulta SQL para atualizar o produto
    $sql_editar = "UPDATE produtos SET nome = '$edicao_nome', descricao = '$edicao_descricao', valor_compra = '$edicao_valor_compra', porcentagem_lucro = '$edicao_porcentagem_lucro', valor_venda = '$edicao_valor_venda', quantidade_em_estoque = '$edicao_quantidade_em_estoque' WHERE id = $edicao_id";
    if ($conexao->query($sql_editar) === TRUE) {
        $_SESSION["mensagem"] = "Produto editado com sucesso!";
        header("Location: cadprod.php"); // Redirecionar para a lista de produtos após a edição
        exit;
    } else {
        $_SESSION["mensagem"] = "Erro ao editar o produto: " . $conexao->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Editar Produto</title>
</head>
<body>
    <h2>Editar Produto</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div><input type="hidden" name="edicao_id" value="<?php echo $edicao_id; ?>">
        <label for="edicao_nome">Nome do Produto:</label>
        <input type="text" id="edicao_nome" name="edicao_nome" value="<?php echo $edicao_nome; ?>" required>
        <label for="edicao_descricao">Descrição:</label>
        <input type="text" id="edicao_descricao" name="edicao_descricao" value="<?php echo $edicao_descricao; ?>" required></div>
        <div><label for="edicao_valor_compra">Valor de Compra:</label>
        <input type="number" id="edicao_valor_compra" name="edicao_valor_compra" value="<?php echo $edicao_valor_compra; ?>" required>
        <label for="edicao_porcentagem_lucro">Porcentagem de Lucro:</label>
        <input type="number" id="edicao_porcentagem_lucro" name="edicao_porcentagem_lucro" value="<?php echo $edicao_porcentagem_lucro; ?>" required ></div>
        <label for="edicao_valor_venda">Valor de Venda:</label>
        <input type="number" id="edicao_valor_venda" name="edicao_valor_venda" value="<?php echo $edicao_valor_venda; ?>" required readonly>
        <label for="edicao_quantidade_em_estoque">Quantidade em Estoque:</label>
        <input type="number" id="edicao_quantidade_em_estoque" name="edicao_quantidade_em_estoque" value="<?php echo $edicao_quantidade_em_estoque; ?>" required>
        <input type="submit" name="editar" value="Salvar">
    </form>

    <script>
// Função para calcular a porcentagem de lucro e o valor de venda
function calcularValores() {
    var valorCompra = parseFloat(document.getElementById("edicao_valor_compra").value);
    var porcentagemLucro = parseFloat(document.getElementById("edicao_porcentagem_lucro").value);
    
    if (!isNaN(valorCompra) && !isNaN(porcentagemLucro)) {
        var valorVenda = valorCompra + (valorCompra * porcentagemLucro / 100);
        document.getElementById("edicao_valor_venda").value = valorVenda.toFixed(2);
    }
}

        // Adicione um evento de mudança aos campos de valor de compra e porcentagem de lucro
        document.getElementById("edicao_valor_compra").addEventListener("change", calcularValores);
        document.getElementById("edicao_porcentagem_lucro").addEventListener("change", calcularValores);
    </script>
</body>
</html>
