<?php
include_once('conexao.php');

// Conexão com o banco de dados
$conexao = new mysqli($hostname, $username, $password, $database);

// Verifica se houve erro na conexão
if ($conexao->connect_error) {
    die("Erro na conexão com o banco de dados: " . $conexao->connect_error);
}

// Defina o conjunto de caracteres para UTF-8, se necessário
$conexao->set_charset("utf8");

// Buscar o cliente pelo ID (substitua 1 pelo ID do cliente desejado)
$cliente = buscarClientePorID($conexao, 1);

// Buscar produtos disponíveis
$produtos = buscarProdutosDisponiveis($conexao);

// Exibir informações do cliente e produtos na tela de venda
if ($cliente) {
    // Exibir informações do cliente (por exemplo, nome)
    echo "Cliente: " . $cliente["nome"] . "<br>";
}

if (!empty($produtos)) {
    // Exibir lista de produtos disponíveis
    echo "<h3>Produtos Disponíveis:</h3>";
    foreach ($produtos as $produto) {
        echo "ID: " . $produto["id"] . " - Nome: " . $produto["nome"] . " - Preço: R$ " . $produto["preco"] . "<br>";
    }
} else {
    echo "Nenhum produto disponível.";
}

function buscarClientePorID($conexao, $clienteID) {
    $sql = "SELECT * FROM clientes WHERE id = ?";
    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $clienteID);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    } else {
        return null; // Cliente não encontrado
    }
}

function buscarProdutosDisponiveis($conexao) {
    $sql = "SELECT * FROM produtos";
    $resultado = $conexao->query($sql);
    
    if ($resultado->num_rows > 0) {
        return $resultado->fetch_all(MYSQLI_ASSOC);
    } else {
        return array(); // Nenhum produto encontrado
    }
}

?>
<!DOCTYPE html>
<html>
<head>
    <title>Tela de Venda</title>
</head>
<body>
    <h2>Tela de Venda</h2>
    
    <!-- Seleção de Cliente -->
    <form method="post" action="processar_venda.php">
        <label for="cliente">Selecione o Cliente:</label>
        <select id="cliente" name="cliente" required>
            <!-- Opções de clientes obtidas do banco de dados -->
            <option value="1">Cliente 1</option>
            <option value="2">Cliente 2</option>
            <!-- Adicione mais opções conforme necessário -->
        </select>
        <br>

        <!-- Lançamento de Produtos -->
        <label for="produto">Selecione o Produto:</label>
        <select id="produto" name="produto" required>
            <!-- Opções de produtos obtidas do banco de dados -->
            <option value="1">Produto 1 - R$ 10.00</option>
            <option value="2">Produto 2 - R$ 15.00</option>
            <!-- Adicione mais opções conforme necessário -->
        </select>
        <label for="quantidade">Quantidade:</label>
        <input type="number" id="quantidade" name="quantidade" required>
        <button type="button" id="adicionar_produto">Adicionar Produto</button>
        <br>

        <!-- Lista de Produtos Adicionados -->
        <table>
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Preço Unitário</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody id="lista_produtos">
                <!-- Os produtos adicionados serão exibidos aqui -->
            </tbody>
        </table>

        <!-- Finalização da Venda -->
        <button type="button" id="finalizar_venda">Finalizar Venda</button>
    </form>

    <script>
        // Lógica JavaScript para adicionar produtos à lista
        document.getElementById('adicionar_produto').addEventListener('click', function () {
            // Lógica para adicionar o produto selecionado à lista
            // Atualizar a tabela com os produtos adicionados
        });

        // Lógica JavaScript para finalizar a venda
        document.getElementById('finalizar_venda').addEventListener('click', function () {
            // Direcionar para a tela de seleção de forma de pagamento
        });
    </script>
</body>
</html>
