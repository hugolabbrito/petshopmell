<?php
include_once("conexao.php");

    // Verifique se a conexão foi bem-sucedida
    if ($conexao->connect_error) {
        die("Erro de conexão com o banco de dados: " . $conexao->connect_error);
    }

    // Obtenha as informações de cadastro do formulário
    $nome = $_POST["nome"];
    $email = $_POST["email"];
    $senha = $_POST["senha"];

    // Consulta SQL para inserir o novo usuário no banco de dados
    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES ('$nome', '$email', '$senha')";

    if ($conexao->query($sql) === TRUE) {
        // Cadastro bem-sucedido
        $_SESSION["mensagem"] = "Cadastro realizado com sucesso!";
        header("Location: login.php"); // Redirecionar para a tela de login
    } else {
        // Erro no cadastro
        $_SESSION["mensagem"] = "Erro no cadastro: " . $conexao->error;
    }

    // Feche a conexão com o banco de dados
    $conexao->close();

?>

<!DOCTYPE html>
<html>
<head>
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>
    <?php
    if (isset($_SESSION["mensagem"])) {
        echo "<p>" . $_SESSION["mensagem"] . "</p>";
        unset($_SESSION["mensagem"]);
    }
    ?>
    <form method="post" action="<?php echo $_SERVER["PHP_SELF"]; ?>">
        <label for="nome">Nome:</label>
        <input type="text" id="nome" name="nome" required><br><br>
        <label for="email">Email:</label>
        <input type="text" id="email" name="email" required><br><br>
        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required><br><br>
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>
