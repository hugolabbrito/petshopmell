<?php
session_start();

// Conecte-se ao banco de dados (substitua com suas credenciais)
$conexao = new mysqli("localhost", "hugo", "leokgb", "petshopmell");

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Verifica se o formulário de relatório foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_inicio = $_POST["data_inicio"];
    $data_fim = $_POST["data_fim"];

    // Verifique se as datas estão no formato correto (YYYY-MM-DD) e dentro do período máximo de 90 dias
    $data_valida = true;
    $data_atual = date("Y-m-d");
    $data_limite = date("Y-m-d", strtotime("-90 days"));

    if (!preg_match("/^\d{4}-\d{2}-\d{2}$/", $data_inicio) || !preg_match("/^\d{4}-\d{2}-\d{2}$/", $data_fim)) {
        $_SESSION["mensagem"] = "Formato de data inválido. Use o formato YYYY-MM-DD.";
        $data_valida = false;
    } elseif ($data_inicio > $data_atual || $data_fim > $data_atual) {
        $_SESSION["mensagem"] = "As datas não podem ser posteriores à data atual.";
        $data_valida = false;
    } elseif ($data_inicio < $data_limite || $data_fim < $data_limite) {
        $_SESSION["mensagem"] = "O período máximo permitido é de 90 dias a partir da data atual.";
        $data_valida = false;
    }

    if ($data_valida) {
        // Consulta SQL para buscar as vendas no período especificado
        $sql = "SELECT * FROM vendas WHERE data_venda BETWEEN '$data_inicio' AND '$data_fim'";
        $resultado = $conexao->query($sql);

        if ($resultado->num_rows > 0) {
            // Exibir o relatório de vendas
            $_SESSION["relatorio_vendas"] = $resultado->fetch_all(MYSQLI_ASSOC);
        } else {
            $_SESSION["mensagem"] = "Nenhuma venda encontrada no período especificado.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Relatório de Vendas</title>
</head>
<body>
    <h2>Relatório de Vendas</h2>
    <?php
    if (isset($_SESSION["mensagem"])) {
        echo "<p>" . $_SESSION["mensagem"] . "</p>";
        unset($_SESSION["mensagem"]);
    }
    ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="data_inicio">Data de Início (YYYY-MM-DD):</label>
        <input type="text" id="data_inicio" name="data_inicio" required><br><br>
        <label for="data_fim">Data de Fim (YYYY-MM-DD):</label>
        <input type="text" id="data_fim" name="data_fim" required><br><br>
        <input type="submit" value="Gerar Relatório">
    </form>

    <?php
    if (isset($_SESSION["relatorio_vendas"])) {
        echo "<h3>Relatório de Vendas no Período</h3>";
        echo "<table>";
        echo "<tr><th>ID Venda</th><th>ID Produto</th><th>Quantidade Vendida</th><th>Data da Venda</th></tr>";
        
        foreach ($_SESSION["relatorio_vendas"] as $venda) {
            echo "<tr>";
            echo "<td>" . $venda["id"] . "</td>";
            echo "<td>" . $venda["id_produto"] . "</td>";
            echo "<td>" . $venda["quantidade_vendida"] . "</td>";
            echo "<td>" . $venda["data_venda"] . "</td>";
            echo "</tr>";
        }
        
        echo "</table>";
        unset($_SESSION["relatorio_vendas"]);
    }
    ?>
</body>
</html>
