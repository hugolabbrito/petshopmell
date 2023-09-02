<?php
include_once('conexao.php');
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
