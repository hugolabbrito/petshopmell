<?php
include_once('conexao.php');

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Inicialize variáveis
$produto_id = "";
$produto_nome = "";
$quantidade = "";
$valor_unitario = "";
$valor_total = "";
$subtotal = 0;
$desconto_porcentagem = "";
$valor_desconto = 0;
$total_venda = 0;

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $produto_id = $_POST["produto_id"];
    $produto_nome = $_POST["produto_nome"];
    $quantidade = $_POST["quantidade"];
    $desconto_porcentagem = $_POST["desconto_porcentagem"];

    // Verificar se o produto existe pelo ID ou pelo nome
    if (!empty($produto_id)) {
        $sql = "SELECT * FROM produtos WHERE id = $produto_id";
    } elseif (!empty($produto_nome)) {
        $sql = "SELECT * FROM produtos WHERE nome LIKE '%$produto_nome%'";
    } else {
        $_SESSION["mensagem"] = "Informe o ID ou o nome do produto.";
    }

    if (isset($sql)) {
        $resultado = $conexao->query($sql);

        if ($resultado->num_rows == 1) {
            $produto = $resultado->fetch_assoc();
            $valor_unitario = $produto["preco_venda"];
            $valor_total = $valor_unitario * $quantidade;

            // Calcular subtotal das vendas
            $subtotal += $valor_total;

            // Calcular valor do desconto
            if (!empty($desconto_porcentagem)) {
                $valor_desconto = ($desconto_porcentagem / 100) * $subtotal;
            }

            // Calcular o valor total da venda
            $total_venda = $subtotal - $valor_desconto;
        } else {
            $_SESSION["mensagem"] = "Produto não encontrado.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tela de Vendas</title>
</head>
<body>
    <h2>Tela de Vendas</h2>
    <?php
    if (isset($_SESSION["mensagem"])) {
        echo "<p>" . $_SESSION["mensagem"] . "</p>";
        unset($_SESSION["mensagem"]);
    }
    ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <div><label for="produto_id">ID do Produto:</label>
        <input type="text" id="produto_id" name="produto_id"></div>
        <div><label for="produto_nome">Nome do Produto:</label>
        <input type="text" id="produto_nome" name="produto_nome"></div>
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required>
        <label for="valor_unitario">Valor Unitário:</label>
        <input type="text" id="valor_unitario" name="valor_unitario" value="<?php echo $valor_unitario; ?>" readonly>
        <label for="valor_total">Valor Total:</label>
        <input type="text" id="valor_total" name="valor_total" value="<?php echo $valor_total; ?>" readonly>
        <label for="subtotal">Subtotal:</label>
        <input type="text" id="subtotal" name="subtotal" value="<?php echo $subtotal; ?>" readonly>
        <label for="desconto_porcentagem">Desconto (%):</label>
        <input type="text" id="desconto_porcentagem" name="desconto_porcentagem">
        <label for="valor_desconto">Valor Desconto:</label>
        <input type="text" id="valor_desconto" name="valor_desconto" value="<?php echo $valor_desconto; ?>" readonly>
        <label for="total_venda">Total da Venda:</label>
        <input type="text" id="total_venda" name="total_venda" value="<?php echo $total_venda; ?>" readonly>
        <input type="submit" value="Calcular">
    </form>
</body>
</html>
