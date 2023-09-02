<?php
include_once('conexao.php');

/* // Conexão com o banco de dados
$conexao = new mysqli($hostname, $username, $password, $database); */

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
//if ($cliente) {
//    // Exibir informações do cliente (por exemplo, nome)
//    echo "Cliente: " . $cliente["nome"] . "<br>";
//}

if (!empty($produtos)) {
    // Exibir lista de produtos disponíveis
    echo "<h3>Produtos Disponíveis:</h3>";
    foreach ($produtos as $produto) {
        echo "Código: " . $produto["id"] . " - Nome: " . $produto["nome"] . "<br>";
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
            <?php
            // Incluir código de conexão com o banco de dados
            include "conexao.php";

            // Consulta SQL para buscar todos os clientes
            $sql = "SELECT id, nome FROM clientes";
            $resultado = $conexao->query($sql);

            if ($resultado->num_rows > 0) {
                while ($row = $resultado->fetch_assoc()) {
                    $clienteID = $row["id"];
                    $clienteNome = $row["nome"];
                    echo "<option value='$clienteID'>$clienteNome</option>";
                }
            } else {
                echo "<option value='' disabled>Nenhum cliente encontrado</option>";
            }

            // Fechar a conexão com o banco de dados
            $conexao->close();
            ?>
            
            <!-- <option value="1">Cliente 1</option>
            <option value="2">Cliente 2</option> -->
            <!-- Adicione mais opções conforme necessário -->
        </select>
        <br>

        <!-- Lançamento de Produtos -->
        <label for="produto">Selecione o Produto:</label>
        <select id="produto" name="produto" required>
            <!-- Opções de produtos obtidas do banco de dados -->
             <?php
            // Incluir código de conexão com o banco de dados
            include "conexao.php";

            // Consulta SQL para buscar todos os produtos disponíveis
            $sql = "SELECT id, nome, valor_venda FROM produtos";
            $resulprod = $conexao->query($sql);

            if ($resulprod->num_rows > 0) {
                while ($row = $resulprod->fetch_assoc()) {
                    $produtoID = $row["id"];
                    $produtoNome = $row["nome"];
                    $produtoPreco = $row["valor_venda"];
                    echo "<option value='$produtoID'>$produtoNome - R$ $produtoPreco</option>";
                }
            } else {
                echo "<option value='' disabled>Nenhum produto disponível</option>";
            }

            // Fechar a conexão com o banco de dados
            $conexao->close();
            ?>
            <!-- <option value="1">Produto 1 - R$ 10.00</option>
            <option value="2">Produto 2 - R$ 15.00</option> -->
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
<!-- Valor Total -->
        <div>
            <label for="valor_total">Valor Total:</label>
            <span id="valor_total">R$ 0.00</span>
        </div>
        <!-- Finalização da Venda -->
        <button type="submit" id="finalizar_venda">Finalizar Venda</button>
    </form>
    <script>
        // Variável para armazenar os produtos adicionados
        var produtosAdicionados = [];

        document.getElementById('adicionar_produto').addEventListener('click', function () {
            var produtoSelecionado = document.getElementById('produto');
            var quantidade = document.getElementById('quantidade').value;
            var produtoOption = produtoSelecionado.options[produtoSelecionado.selectedIndex];
            var produtoID = produtoOption.value;
            var produtoNome = produtoOption.text.split('-')[0].trim();
            var produtoPreco = parseFloat(produtoOption.text.split('R$')[1].trim());

            // Calcular subtotal
            var subtotal = quantidade * produtoPreco;

            // Adicionar o produto à lista
            produtosAdicionados.push({
                id: produtoID,
                nome: produtoNome,
                quantidade: quantidade,
                preco: produtoPreco,
                subtotal: subtotal
            });

            // Atualizar a tabela de produtos adicionados
            atualizarTabelaProdutos();

            // Limpar os campos de seleção e quantidade
            produtoSelecionado.selectedIndex = 0;
            document.getElementById('quantidade').value = '';

            // Calcular e exibir o valor total
            calcularValorTotal();
        });

        function atualizarTabelaProdutos() {
            var tabela = document.getElementById('lista_produtos');
            tabela.innerHTML = '';

            for (var i = 0; i < produtosAdicionados.length; i++) {
                var produto = produtosAdicionados[i];
                var row = tabela.insertRow();
                var cellNome = row.insertCell(0);
                var cellQuantidade = row.insertCell(1);
                var cellPreco = row.insertCell(2);
                var cellSubtotal = row.insertCell(3);

                cellNome.innerHTML = produto.nome;
                cellQuantidade.innerHTML = produto.quantidade;
                cellPreco.innerHTML = 'R$ ' + produto.preco.toFixed(2);
                cellSubtotal.innerHTML = 'R$ ' + produto.subtotal.toFixed(2);
            }
        }
        function calcularValorTotal() {
            var valorTotal = 0;

            for (var i = 0; i < produtosAdicionados.length; i++) {
                valorTotal += produtosAdicionados[i].subtotal;
            }

            document.getElementById('valor_total').textContent = 'R$ ' + valorTotal.toFixed(2);
        }
        
    </script>
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
