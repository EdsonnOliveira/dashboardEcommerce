<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Follow | Painel de Controle | Ecommerce</title>
        <?php include('PHP/meta.php') ?>
    </head>
    <body>
        <?php include('PHP/header.php') ?>
        <main>
            <h1 class='txtWhite txtTitle txt500'>Painel de Controle</h1>
        </main>
    </body>
</html>