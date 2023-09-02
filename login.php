<?php
session_start();

// Verifique se o formulário de login foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Conecte-se ao banco de dados (substitua com suas credenciais)
    $conexao = new mysqli("localhost", "hugo", "leokgb", "petshopmell");

    // Verifique se a conexão foi bem-sucedida
    if ($conexao->connect_error) {
        die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
    }

    // Obtenha as informações de login do formulário
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Consulta SQL para verificar as credenciais
    $sql = "SELECT id, nome FROM usuarios WHERE email = '$email' AND senha = '$senha'";
    $resultado = $conexao->query($sql);

    if ($resultado->num_rows == 1) {
        // Login bem-sucedido
        $row = $resultado->fetch_assoc();
        $_SESSION["usuario_id"] = $row["id"];
        $_SESSION["usuario_nome"] = $row["nome"];
        header("Location: painel.php"); // Redirecionar para a página do painel
    } else {
        // Login falhou
        $erro = "Credenciais inválidas. Tente novamente.";
    }

    // Feche a conexão com o banco de dados
    $conexao->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Tela de Login</title>
</head>
<body>
    <h2>Tela de Login</h2>
    <?php if (isset($erro)) { echo "<p>$erro</p>"; } ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required><br><br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>
        <input type="submit" value="Login">
    </form>
</body>
</html>
