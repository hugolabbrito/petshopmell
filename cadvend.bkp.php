<?php
session_start();

// Conecte-se ao banco de dados (substitua com suas credenciais)
$conexao = new mysqli("localhost", "hugo", "leokgb", "petshopmell");

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Verifica se o formulário de venda foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtenha os dados do formulário
    $id_produto = $_POST["id_produto"];
    $quantidade_vendida = $_POST["quantidade_vendida"];
    $data_venda = date("Y-m-d"); // Use a data atual

    // Verifique se há estoque suficiente para a venda
    $sql_check_estoque = "SELECT quantidade_em_estoque FROM produtos WHERE id=$id_produto";
    $resultado = $conexao->query($sql_check_estoque);

    if ($resultado->num_rows == 1) {
        $row = $resultado->fetch_assoc();
        $quantidade_em_estoque = $row["quantidade_em_estoque"];

        if ($quantidade_vendida <= $quantidade_em_estoque) {
            // Realize a venda
            $sql_venda = "INSERT INTO vendas (id_produto, quantidade_vendida, data_venda) VALUES ($id_produto, $quantidade_vendida, '$data_venda')";
            if ($conexao->query($sql_venda) === TRUE) {
                // Atualize a quantidade em estoque
                $nova_quantidade_em_estoque = $quantidade_em_estoque - $quantidade_vendida;
                $sql_atualiza_estoque = "UPDATE produtos SET quantidade_em_estoque = $nova_quantidade_em_estoque WHERE id = $id_produto";
                $conexao->query($sql_atualiza_estoque);

                $_SESSION["mensagem"] = "Venda realizada com sucesso!";
            } else {
                $_SESSION["mensagem"] = "Erro ao realizar a venda: " . $conexao->error;
            }
        } else {
            $_SESSION["mensagem"] = "Quantidade em estoque insuficiente para esta venda.";
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
        <label for="id_produto">ID do Produto:</label>
        <input type="number" id="id_produto" name="id_produto" required><br><br>
        <label for="quantidade_vendida">Quantidade Vendida:</label>
        <input type="number" id="quantidade_vendida" name="quantidade_vendida" required><br><br>
        <input type="submit" value="Realizar Venda">
    </form>
</body>
</html>
