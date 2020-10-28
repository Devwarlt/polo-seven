<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Polo Seven - Início</title>
</head>
<body>
<h1>Polo Seven - Início</h1>
<hr/>
<form action="/php/MVCRouter.php" method="post">
    <input type="hidden" id="login_flag" name="target" value="login"/>
    <h2>Login</h2>
    <br/>
    <label for="nome">Nome:</label>
    <br/>
    <input type="text" id="nome" name="nome" placeholder="Nome"/>
    <br/>
    <label for="senha">Senha:</label>
    <br/>
    <input type="password" id="senha" name="senha" placeholder="Senha"/>
    <br/><br/>
    <input type="submit" value="Entrar"/>&nbsp;
    <input type="reset" value="Resetar"/>
</form>
</body>
</html>