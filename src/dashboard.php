<?php
include "php/controller/LoginController.php";

session_start();
?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Polo Seven - Dashboard</title>
</head>
<style>
    .sair-btn {
        width: 96px;
        color: white;
        font-weight: bold;
        background-color: rgba(255, 0, 0, 0.56);
        border-width: 2px;
        border-color: rgba(255, 0, 0, 0.78);
    }
</style>
<body>
<h1>Polo Seven - Dashboard</h1>
<hr/>
<table border="0" width="100%">
    <tr>
        <td width="75%">
            Seja bem-vindo(a), <strong><?php echo $_SESSION[LOGIN_NAME] ?></strong>!
        </td>
        <td align="right">
            <form action="php/MVCRouter.php" method="post">
                <input type="hidden" name="controller" value="login"/>
                <input type="hidden" name="logout"/>
                <input class="sair-btn" type="submit" value="Sair"/>
            </form>
        </td>
    </tr>
</table>
</body>
</html>