<?php
// Conexão ao banco de dados (substitua com suas credenciais)
$conexao = new mysqli("localhost", "hugo", "leokgb", "petshopmell");

// Verifique se a conexão foi bem-sucedida
if ($conexao->connect_error) {
    die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
}

// Consulta SQL para buscar todos os produtos
$sql = "SELECT * FROM produtos";
$resultado = $conexao->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Lista de Produtos</title>
</head>
<body>
    <h2>Lista de Produtos</h2>
    <form method="post" action="cadprod.php">
        <input type="submit" value="Incluir">
    </form>
    <form method="post" action="cadprod.php">
        <table>
            <tr>
                <th>ID</th>
                <th>Nome do Produto</th>
                <th>Preço</th>
                <th>Selecionar</th>
            </tr>
            <?php
            while ($row = $resultado->fetch_assoc()) {
                echo "<tr>";
                echo "<td>" . $row["id"] . "</td>";
                echo "<td>" . $row["nome"] . "</td>";
                echo "<td>" . $row["preco"] . "</td>";
                echo "<td><input type='checkbox' name='selecionados[]' value='" . $row["id"] . "'></td>";
                echo "</tr>";
            }
            ?>
        </table>
        <input type="submit" name="editar" value="Editar">
        <input type="submit" name="excluir" value="Excluir" onclick="return confirm('Deseja excluir os produtos selecionados?')">
    </form>
</body>
</html>
