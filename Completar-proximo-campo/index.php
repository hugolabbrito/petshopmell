<!DOCTYPE html>
<html lang="pt-br">
	<head>
		<meta charset="utf-8">
		<title>Completar proximos campos</title>
		<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.5.2/jquery.min.js"></script>
	</head>
	<body>
		<script type='text/javascript'>
			$(document).ready(function(){
				$("input[name='matricula']").blur(function(){
					var $nome_aluno = $("input[name='nome_aluno']");
					var $rg = $("input[name='rg']");
					$.getJSON('function.php',{ 
						matricula: $( this ).val() 
					},function( json ){
						$nome_aluno.val( json.nome_aluno );
						$rg.val( json.rg );
					});
				});
			});
		</script>
		<h1>Aluno</h1>
		<form method="POST" action="">
			<label>Matricula</label>
			<input type="text" class="form-control" name="matricula"><br><br>
			
			<label>Nome_Aluno</label>
			<input type="text" class="form-control" name="nome_aluno"><br><br>
			
			<label>Rg</label>
			<input type="text" class="form-control" name="rg"><br><br>
			
			<input type="submit" value="Editar">
		</form>
	</body>
</html>