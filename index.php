<?php
    include('PHP/conn.php');

    if (isset($_GET['close'])) {
        session_destroy();
        header('location:index.php');
        die();
    }

    if (isset($_SESSION['LOGIN'])) {
        header("location:dashboard.php");
        die();
    }

    if (isset($_POST['entrar'])) {
        $erro = '';
        $SQL = $conn->prepare('SELECT * FROM usuarios WHERE Email=? AND Senha=? AND Situacao=1 LIMIT 1');
        $SQL->execute([$_POST['email'], $_POST['senha']]);

        if ($SQL->rowCount() == 0)
            $erro = 'Usuário não encontrado!';

        $SQLUser = $SQL->fetch();
        $SQL = $conn->prepare('SELECT * FROM filial WHERE IDFilial=? AND Situacao=? LIMIT 1');
        $SQL->execute([$SQLUser['IDFilial'], '1']);

        if ($SQL->rowCount() == 0)
            $erro = 'Filial bloqueada! Entre em contato com a equipe de suporte.';

        if ($erro == '') {
            $_SESSION['LOGIN']['ID']       = $SQLUser['IDUsuario'];
            $_SESSION['LOGIN']['IDFilial'] = $SQLUser['IDFilial'];
            $_SESSION['LOGIN']['Nome']     = $SQLUser['Nome'];

            header('location:dashboard.php');
        } else {
            echo "<script>alert('$erro')</script>";
        }
    }

?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Follow | Ecommerce</title>
        <?php include('PHP/meta.php') ?>
        <link rel="stylesheet" type="text/css" href="CSS/login.css">
    </head>
    <body style='height: 100vh; display:flex; justify-content: center; align-items: center; background-color: #2B2C30; margin:0;'>
        <main>
            <div>
                <img src="IMG/Logo/Logo.png" alt="Logo">
            </div>
            <div>
                <h1 class='txtWhite txt700'>ECOMMERCE</h1>
            </div>
            <div>
                <form method='post'>
                    <label>
                        <h3 class='txtWhite txt600'>E-MAIL</h3>
                        <input type="email" name='email' class='input ipRound' placeholder='Seu e-mail' autofocus required>
                    </label>
                    <label>
                        <h3 class='txtWhite txt600'>SENHA</h3>
                        <input type="password" name='senha' class='input ipRound' placeholder='Sua senha' required>
                    </label>
                    <input type="submit" name='entrar' class='button btVar btRound txtWhite txt600' value='ENTRAR'>
                </form>
            </div>
        </main>
    </body>
</html>