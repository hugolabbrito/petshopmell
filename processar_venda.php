<?php
// Inclua o código de conexão com o banco de dados
include "conexao.php";

// Verifique se o formulário de venda foi submetido
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Dados da venda
    $clienteID = $_POST["cliente"]; // ID do cliente selecionado
    $produtosAdicionados = $_POST["produtos"]; // Array de produtos adicionados

    // Inicie uma transação para garantir a consistência dos dados
    $conexao->begin_transaction();

    try {
        // Insira os detalhes da venda na tabela "vendas"
        $sqlVenda = "INSERT INTO vendas (id_cliente, data_venda) VALUES (?, NOW())";
        $stmtVenda = $conexao->prepare($sqlVenda);
        $stmtVenda->bind_param("i", $clienteID);
        $stmtVenda->execute();
        $idVenda = $stmtVenda->insert_id;

        // Insira os produtos vendidos na tabela "vendas_produtos"
        foreach ($produtosAdicionados as $produto) {
            $produtoID = $produto["id"];
            $quantidade = $produto["quantidade_vendida"];

            // Consulte o preço do produto no banco de dados
            $sqlPrecoProduto = "SELECT valor_venda FROM produtos WHERE id = ?";
            $stmtPrecoProduto = $conexao->prepare($sqlPrecoProduto);
            $stmtPrecoProduto->bind_param("i", $produtoID);
            $stmtPrecoProduto->execute();
            $resultadoPrecoProduto = $stmtPrecoProduto->get_result();

            if ($resultadoPrecoProduto->num_rows > 0) {
                $row = $resultadoPrecoProduto->fetch_assoc();
                $precoProduto = $row["valor_venda"];

                // Insira os detalhes do produto vendido
                $sqlVendaProduto = "INSERT INTO vendas (id_venda, id_produto, quantidade_vendida, valor_total) VALUES (?, ?, ?, ?)";
                $stmtVendaProduto = $conexao->prepare($sqlVendaProduto);
                $stmtVendaProduto->bind_param("iiid", $idVenda, $produtoID, $quantidade, $precoProduto);
                $stmtVendaProduto->execute();
            }
        }

        // Finalize a transação
        $conexao->commit();
        echo "Venda registrada com sucesso!";
    } catch (Exception $e) {
        // Em caso de erro, faça um rollback na transação
        $conexao->rollback();
        echo "Erro ao registrar a venda: " . $e->getMessage();
    }
}

// Feche a conexão com o banco de dados
$conexao->close();
?>


<!DOCTYPE html>
<html>
<head>
    <title>Finalização da Venda</title>
</head>
<body>
    <h2>Finalização da Venda</h2>
    <form method="post" action="processar_finalizacao_venda.php">
        <!-- Lista de produtos da venda (pode incluir informações aqui) -->

        <!-- Valor Total da Venda (obtido da venda em andamento) -->
        <label for="valor_total">Valor Total:</label>
        <input type="text" id="valor_total" name="valor_total" value="100.00" readonly>
        <br>

        <!-- Formas de Pagamento -->
        <label for="forma_pagamento">Forma(s) de Pagamento:</label><br>
        <input type="checkbox" id="dinheiro" name="formas_pagamento[]" value="dinheiro">
        <label for="dinheiro">Dinheiro</label><br>
        <input type="checkbox" id="pix" name="formas_pagamento[]" value="pix">
        <label for="pix">PIX</label><br>
        <input type="checkbox" id="cartao_credito" name="formas_pagamento[]" value="cartao_credito">
        <label for="cartao_credito">Cartão de Crédito</label><br>
        <input type="checkbox" id="cartao_debito" name="formas_pagamento[]" value="cartao_debito">
        <label for="cartao_debito">Cartão de Débito</label><br>
        <input type="checkbox" id="outra" name="formas_pagamento[]" value="outra">
        <label for="outra">Outra (Especificar):</label>
        <input type="text" id="outra_forma" name="outra_forma">
        <br>

        <!-- Porcentagem de Desconto -->
        <label for="desconto">Porcentagem de Desconto (%):</label>
        <input type="number" id="desconto" name="desconto" min="0" max="100">
        <br>

        <!-- Valor Pago -->
        <label for="valor_pago">Valor Pago:</label>
        <input type="number" id="valor_pago" name="valor_pago">
        <br>

        <!-- Valor Restante -->
        <label for="valor_restante">Valor Restante:</label>
        <input type="text" id="valor_restante" name="valor_restante" readonly>
        <br>

        <!-- Botão para Finalizar a Venda -->
        <input type="submit" value="Finalizar Venda">
    </form>

    <script>
        // Lógica JavaScript para calcular o valor restante com base no valor pago e desconto
        document.getElementById('valor_pago').addEventListener('input', calcularValorRestante);
        document.getElementById('desconto').addEventListener('input', calcularValorRestante);

        function calcularValorRestante() {
            const valorTotal = parseFloat(document.getElementById('valor_total').value);
            const valorPago = parseFloat(document.getElementById('valor_pago').value);
            const desconto = parseFloat(document.getElementById('desconto').value || 0);

            const valorComDesconto = valorTotal - (valorTotal * (desconto / 100));
            const valorRestante = valorComDesconto - valorPago;

            document.getElementById('valor_restante').value = valorRestante.toFixed(2);

            // Habilitar ou desabilitar o botão de finalização com base no valor pago igual ao valor total
            const botaoFinalizar = document.querySelector('input[type="submit"]');
            botaoFinalizar.disabled = valorPago !== valorComDesconto;
        }
    </script>
</body>
</html>
