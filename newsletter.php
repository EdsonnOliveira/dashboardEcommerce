<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");

    if (isset($_POST['salvarEmail'])) {
        $SQL = $conn->prepare('INSERT INTO newsletter (IDFilial, Email) VALUES (?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['email']]);
        header("location: newsletter.php");
    }

    if (isset($_POST['excluirEmail'])) {
        $SQL = $conn->prepare('DELETE FROM newsletter WHERE IDNewsLetter=? LIMIT 1');
        $SQL->execute([$_GET['IDEmail']]);
        header("location: newsletter.php");
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Follow | NewsLetter | Ecommerce</title>
        <?php include('PHP/meta.php') ?>
        <link rel="stylesheet" type="text/css" href="CSS/newsletter.css">
    </head>
    <body>
        <?php include('PHP/header.php') ?>
        <main>
            <h2 class='txtWhite txtPages txtUpper txt400'><a href="dashboard.php" class='txtWhite txtNoDecoration'>Painel</a> / NewsLetter</h2>
            <h1 class='txtWhite txtTitle txt500'>NewsLetter</h1>
            <div class='divFlex' style='margin-top:15px'>
                <button onclick="showModal('modalNewEmail')" id='add'>
                    <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                        <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                        <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                    </figure>
                    <h3 class='txt600'>E-MAIL</h3>
                </button>
                <?php
                    $SQL = $conn->prepare('SELECT * FROM newsletter WHERE IDFilial=?');
                    $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);
                    $SQL = $SQL->fetchAll();
                    foreach ($SQL as $value) {
                        echo "<a href='?IDEmail=".$value['IDNewsLetter']."' class='button btVar btRound txtWhite txtNoDecoration txt600 btEmail'>" . $value['Email'] . "</a>";
                    }
                ?>
                <div class='filter'>
                    <form>
                        <input type="text" name='search' class='input ip100 ipRound txtBlack txt600' placeholder='PROCURAR...' style='height: 15px; margin-bottom: 10px;'>
                        <input type="submit" class='button btVar btMedium bt100 btRound txtWhite txt700' value='FILTRAR'>
                    </form>
                </div>
            </div>
            <div id='modalNewEmail' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Novo Email</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <div style='display:flex; justify-content:center; margin-bottom:13px;'>
                                    <label style='margin-right: 13px'>
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>E-MAIL</h3>
                                        <input type="email" name='email' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o e-mail' required>
                                    </label>
                                </div>
                                <input type="submit" name='salvarEmail' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDEmail'])) {
                    $SQLEmail = $conn->prepare("SELECT * FROM newsletter WHERE IDNewsLetter=? LIMIT 1");
                    $SQLEmail->execute([$_GET['IDEmail']]);
                    $SQLEmail = $SQLEmail->fetch();
                    $email = $SQLEmail['Email'];
            ?>
            <div id='modalDeleteEmail' class='modalContainer closeModal mdShow' onclick="location. href='newsletter.php'">
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Excluir Email</h1>
                        <button class='button closeModal' onclick="location. href='newsletter.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <h1 class='txtBlack txtCenter' style='margin-top:10px;'>Você tem certeza?</h1>
                                <h2 class='txtblack txtCenter txt500' style='margin-bottom:15px;'>Deseja excluir o email <b><?php echo $email;?></b> ?</h2>
                                <input type="submit" name='excluirEmail' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR EMAIL'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
        </main>
    </body>
</html>