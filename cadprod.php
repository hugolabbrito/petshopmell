<?php
include_once('conexao.php');

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Variáveis de edição
$id = "";
$nome = "";
$descricao = "";
$valor_compra = "";
$porcentagem_lucro = "";
$valor_venda = "";
$quantidade_em_estoque = "";

// Verifique se o formulário foi enviado para inclusão ou edição
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["incluir"])) {
        // Inserir novo produto
        $nome = $_POST["nome"];
        $descricao = $_POST["descricao"];
        $valor_compra = $_POST["valor_compra"];
        $valor_venda = $_POST["valor_venda"];
        $quantidade_em_estoque = $_POST["quantidade_em_estoque"];
        
        // Calcular a porcentagem de lucro
        $porcentagem_lucro = (($valor_venda - $valor_compra) / $valor_compra) * 100;

        // Consulta SQL para inserir um novo produto
        $sql_inserir = "INSERT INTO produtos (nome, descricao, valor_compra, porcentagem_lucro, valor_venda, quantidade_em_estoque) VALUES ('$nome', '$descricao', '$valor_compra', '$porcentagem_lucro', '$valor_venda', '$quantidade_em_estoque')";
        if ($conexao->query($sql_inserir) === TRUE) {
            $_SESSION["mensagem"] = "Produto inserido com sucesso!";
        } else {
            $_SESSION["mensagem"] = "Erro ao inserir o produto: " . $conexao->error;
        }
    } elseif (isset($_POST["editar"])) {
        // Editar produto existente
        $id = $_POST["id"];
        $nome = $_POST["nome"];
        $descricao = $_POST["descricao"];
        $valor_compra = $_POST["valor_compra"];
        $valor_venda = $_POST["valor_venda"];
        $quantidade_em_estoque = $_POST["quantidade_em_estoque"];

        // Calcular a porcentagem de lucro
        $porcentagem_lucro = (($valor_venda - $valor_compra) / $valor_compra) * 100;

        // Consulta SQL para atualizar o produto
        $sql_editar = "UPDATE produtos SET nome = '$nome', descricao = '$descricao', valor_compra = '$valor_compra', porcentagem_lucro = '$porcentagem_lucro', valor_venda = '$valor_venda', quantidade_em_estoque = '$quantidade_em_estoque' WHERE id = $id";
        if ($conexao->query($sql_editar) === TRUE) {
            $_SESSION["mensagem"] = "Produto editado com sucesso!";
        } else {
            $_SESSION["mensagem"] = "Erro ao editar o produto: " . $conexao->error;
        }
    } elseif (isset($_POST["excluir"])) {
        // Excluir produtos selecionados
        $exclusao_ids = $_POST["selecionados"];
        if (!empty($exclusao_ids)) {
            $ids_para_excluir = implode(",", $exclusao_ids);
            $sql_exclusao = "DELETE FROM produtos WHERE id IN ($ids_para_excluir)";
            if ($conexao->query($sql_exclusao) === TRUE) {
                $_SESSION["mensagem"] = "Produtos excluídos com sucesso!";
            } else {
                $_SESSION["mensagem"] = "Erro ao excluir produtos: " . $conexao->error;
            }
        }
    }
}

// Consulta SQL para listar todos os produtos
$sql_lista = "SELECT * FROM produtos";
$resultado_lista = $conexao->query($sql_lista);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Produtos</title>
</head>
<body>
    <h2>Cadastro de Produtos</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <input type="hidden" name="id" value="<?php echo $id; ?>">
        <div><label for="nome">Nome do Produto:</label>
        <input type="text" id="nome" name="nome" value="<?php echo $nome; ?>" required>
        <label for="descricao">Descrição:</label>
        <input type="text" id="descricao" name="descricao" value="<?php echo $descricao; ?>" required></div>
        <label for="valor_compra">Valor de Compra:</label>
        <input type="number" id="valor_compra" name="valor_compra" value="<?php echo $valor_compra; ?>" required>
        <label for="porcentagem_lucro">Porcentagem de Lucro:</label>
        <input type="number" id="porcentagem_lucro" name="porcentagem_lucro" value="<?php echo $porcentagem_lucro; ?>" required>
        <div><label for="valor_venda">Valor de Venda:</label>
        <input type="number" id="valor_venda" name="valor_venda" value="<?php echo $valor_venda; ?>" required readonly>
        <label for="quantidade_em_estoque">Quantidade em Estoque:</label>
        <input type="number" id="quantidade_em_estoque" name="quantidade_em_estoque" value="<?php echo $quantidade_em_estoque; ?>" required></div>
        <input type="submit" name="incluir" value="Incluir">
        <input type="reset" value="Limpar Formulário">
        <!-- <button onclick="window.location.href='editcadprod.php?id=$row[id]'">Editar</button> -->
        <!-- <input type="submit" name="editar" value="Editar"> -->
    </form>

    <h2>Lista de Produtos</h2>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <table>
            <tr>
                <th>ID</th>
                <th>Nome do Produto</th>
                <th>Descrição</th>
                <th>Valor de Compra</th>
                <th>Porcentagem de Lucro</th>
                <th>Valor de Venda</th>
                <th>Quantidade em Estoque</th>
                <th>Selecionar</th>
            </tr>
<?php while ($row = $resultado_lista->fetch_assoc()): ?>
    <tr>
        <td><?php echo $row["id"]; ?></td>
        <td><?php echo $row["nome"]; ?></td>
        <td><?php echo $row["descricao"]; ?></td>
        <td><?php echo $row["valor_compra"]; ?></td>
        <td><?php echo $row["porcentagem_lucro"]; ?></td>
        <td><?php echo $row["valor_venda"]; ?></td>
        <td><?php echo $row["quantidade_em_estoque"]; ?></td>
        <td><input type='checkbox' name='selecionados[]' value='" . $row["id"] . "'></td>
        <td><a href="editcadprod.php?id=<?php echo $row["id"]; ?>">Editar</a></td>
    </tr>
<?php endwhile; ?>
          
        </table>
        
        <input type="submit" name="excluir" value="Excluir" onclick="return confirm('Deseja excluir os produtos selecionados?')">
    </form>

    <script>
        // Função para calcular porcentagem de lucro e valor de venda automaticamente
        function calcularValores() {
            var valorCompra = parseFloat(document.getElementById("valor_compra").value);
            var porcentagemLucro = parseFloat(document.getElementById("porcentagem_lucro").value);
            
            if (!isNaN(valorCompra) && !isNaN(porcentagemLucro)) {
                var valorVenda = valorCompra + (valorCompra * porcentagemLucro / 100);
                document.getElementById("valor_venda").value = valorVenda.toFixed(2);
            }
        }
        
        // Adicione um evento de mudança aos campos de valor de compra e porcentagem de lucro
        document.getElementById("valor_compra").addEventListener("change", calcularValores);
        document.getElementById("porcentagem_lucro").addEventListener("change", calcularValores);
    </script>
</body>
</html>
