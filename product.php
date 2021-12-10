<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");

    // SIZE
    if (isset($_POST['salvarTamanho'])) {
        $SQL = $conn->prepare('INSERT INTO produtos_tamanho (IDFilial, Tamanho, Nome) VALUES (?, ?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['siglaTamanho'], $_POST['nomeTamanho']]);
        header("location: product.php");
    }

    if (isset($_POST['excluirTamanho'])) {
        $SQL = $conn->prepare('DELETE FROM produtos_tamanho WHERE IDTamanho=? LIMIT 1');
        $SQL->execute([$_GET['IDTamanho']]);

        $SQL = $conn->prepare('DELETE FROM produtos_cor_tamanho WHERE IDTamanho=?');
        $SQL->execute([$_GET['IDTamanho']]);
        header("location: product.php");
    }

    // COLOR
    if (isset($_POST['salvarCor'])) {
        $SQL = $conn->prepare('INSERT INTO produtos_cor (IDFilial, Cor, Nome) VALUES (?, ?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['hexCor'], $_POST['nomeCor']]);
        header("location: product.php");
    }

    if (isset($_POST['excluirCor'])) {
        $SQL = $conn->prepare('DELETE FROM produtos_cor WHERE IDCor=? LIMIT 1');
        $SQL->execute([$_GET['IDCor']]);

        $SQL = $conn->prepare('DELETE FROM produtos_cor_tamanho WHERE IDCor=?');
        $SQL->execute([$_GET['IDCor']]);
        header("location: product.php");
    }

    // CATEGORY
    if (isset($_POST['salvarCategoria'])) {
        $menu = 0;
        if (isset($_POST['menuCategoria']))
            $menu = 1;

        $SQL = $conn->prepare('INSERT INTO produtos_categoria (IDFilial, Nome, Menu) VALUES (?, ?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['nomeCategoria'], $menu]);
        header("location: product.php");
    }

    if (isset($_POST['alterarCategoria'])) {
        $menu = 0;
        if (isset($_POST['menuCategoria']))
            $menu = 1;

        $SQL = $conn->prepare('UPDATE produtos_categoria SET Nome=?, Menu=?, WHERE IDCategoria=? LIMIT 1');
        $SQL->execute([$_POST['nomeCategoria'], $menu, $_GET['IDCategoria']]);
        header("location: product.php");
    }

    if (isset($_POST['excluirCategoria'])) {
        $SQL = $conn->prepare('DELETE FROM produtos_categoria WHERE IDCategoria=? LIMIT 1');
        $SQL->execute([$_GET['IDCategoria']]);
        header("location: product.php");
    }

    // BRAND
    if (isset($_POST['salvarMarca'])) {
        $SQL = $conn->prepare('INSERT INTO produtos_marca (IDFilial, Nome) VALUES (?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['nomeMarca']]);
        header("location: product.php");
    }

    if (isset($_POST['excluirMarca'])) {
        $SQL = $conn->prepare('DELETE FROM produtos_marca WHERE IDMarca=? LIMIT 1');
        $SQL->execute([$_GET['IDMarca']]);
        header("location: product.php");
    }

    // UM
    if (isset($_POST['salvarUM'])) {
        $SQL = $conn->prepare('INSERT INTO produtos_um (IDFilial, UM, Nome) VALUES (?, ?, ?)');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $_POST['siglaUM'], $_POST['nomeUM']]);
        header("location: product.php");
    }

    if (isset($_POST['excluirUM'])) {
        $SQL = $conn->prepare('DELETE FROM produtos_um WHERE IDUM=? LIMIT 1');
        $SQL->execute([$_GET['IDUM']]);
        header("location: product.php");
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Follow | Produtos | Ecommerce</title>
        <?php include('PHP/meta.php') ?>
        <link rel="stylesheet" type="text/css" href="CSS/product.css">
    </head>
    <body>
        <?php include('PHP/header.php') ?>
        <main>
            <h2 class='txtPages txtUpper txt400'><a href="dashboard.php" class='txtPages txtNoDecoration'>Painel</a> / Produtos</h2>
            <h1 class='txtTitle txt500 tbWhite'>Produtos</h1>
            <div class='divFlex' style='margin-top:21px'>
                <a onclick="showModal('modalSize')" class='button btVar btRound btMedium txtUpper txtWhite txtNoDecoration txt600' style='margin-right: 15px; margin-bottom: 15px;'>Tamanho</a>
                <a onclick="showModal('modalColor')" class='button btVar btRound btMedium txtUpper txtWhite txtNoDecoration txt600' style='margin-right: 15px; margin-bottom: 15px;'>Cor</a>
                <a onclick="showModal('modalCategory')" class='button btVar btRound btMedium txtUpper txtWhite txtNoDecoration txt600' style='margin-right: 15px; margin-bottom: 15px;'>Categoria</a>
                <a onclick="showModal('modalBrand')" class='button btVar btRound btMedium txtUpper txtWhite txtNoDecoration txt600' style='margin-right: 15px; margin-bottom: 15px;'>Marca</a>
                <a onclick="showModal('modalUM')" class='button btVar btRound btMedium txtUpper txtWhite txtNoDecoration txt600' style='margin-right: 15px; margin-bottom: 15px;'>UM</a>
            </div>
            <div class='divFlex' style='margin-top: 5px;'>
                <section id='product'>
                    <a href="productModify.php" class='txtNoDecoration'>
                        <div class='product'>
                            <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </figure>
                            <h3 class='txt600'>PRODUTO</h3>
                        </div>
                    </a>
                    <?php
                        $SQL = $conn->prepare("SELECT * FROM produtos WHERE Situacao=1 AND IDFilial=?");
                        $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);

                        $SQLProduct = $SQL->fetchAll();
                        foreach ($SQLProduct as $value) {
                            echo "<a href='productModify.php?ID=".$value['IDProduto']."' class='txtNoDecoration'>";
                                echo '<div class="product" style="background-image: url('."'".IMAGE.$value['Imagem']."'".')">';
                                    echo "<div class='price'>";
                                        echo "<h2 class='txtBlack txtCenter txt800'>R$ ". str_replace('.', ',', $value['ValorVenda']) ."</h2>";
                                    echo "</div>";
                                    echo "<div class='name'>";
                                        echo "<h1 class='txtUpper txtWhite txtCenter'>".$value['Nome']."</h1>";
                                    echo "</div>";
                                echo "</div>";
                            echo "</a>";
                        }
                    ?>
                    <!-- <div class='filter'>
                        <form>
                            <input type="text" name='search' class='input ip100 ipRound txtBlack txt600' placeholder='PROCURAR...' style='height: 15px; margin-bottom: 10px;'>
                            <div class='option'>
                                <h3 class='txtWhite'>TAMANHO</h3>
                                <div class='options'>
                                    <?php
                                        $SQLSize = $conn->prepare("SELECT * FROM produtos_tamanho WHERE IDFilial=?");
                                        $SQLSize->execute([$_SESSION['LOGIN']['IDFilial']]);
                                        $SQLSize = $SQLSize->fetchAll();
                                        foreach ($SQLSize as $value) {
                                            echo "<div style='background-color: #232323;'>";
                                                echo "<h4 class='txtWhite'>".$value['Tamanho']."</h4>";
                                            echo "</div>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class='option'>
                                <h3 class='txtWhite'>COR</h3>
                                <div class='options'>
                                    <?php
                                        $SQLColor = $conn->prepare("SELECT * FROM produtos_cor WHERE IDFilial=?");
                                        $SQLColor->execute([$_SESSION['LOGIN']['IDFilial']]);
                                        $SQLColor = $SQLColor->fetchAll();
                                        foreach ($SQLColor as $value) {
                                            echo "<div style='background-color:".$value['Cor']."'></div>";
                                        }
                                    ?>
                                </div>
                            </div>
                            <div class='option'>
                                <h3 class='txtWhite'>CATEGORIA</h3>
                                <div class='options'>
                                    <select name="category" class='input ipRound ip100' style='width:100%'>
                                        <option selected>TODAS</option>
                                        <?php
                                            $SQLCategory = $conn->prepare("SELECT * FROM produtos_categoria WHERE IDFilial=?");
                                            $SQLCategory->execute([$_SESSION['LOGIN']['IDFilial']]);
                                            $SQLCategory = $SQLCategory->fetchAll();
                                            foreach ($SQLCategory as $value) {
                                                echo "<option value='".$value['IDCategoria']."'>".$value['Nome']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class='option'>
                                <h3 class='txtWhite'>MARCA</h3>
                                <div class='options'>
                                    <select name="brand" class='input ipRound ip100' style='width:100%'>
                                        <option selected>TODAS</option>
                                        <?php
                                            $SQLBrand = $conn->prepare("SELECT * FROM produtos_marca WHERE IDFilial=?");
                                            $SQLBrand->execute([$_SESSION['LOGIN']['IDFilial']]);
                                            $SQLBrand = $SQLBrand->fetchAll();
                                            foreach ($SQLBrand as $value) {
                                                echo "<option value='".$value['IDMarca']."'>".$value['Nome']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class='option'>
                                <h3 class='txtWhite'>UNIDADE DE MEDIDA</h3>
                                <div class='options'>
                                    <select name="um" class='input ipRound ip100' style='width:100%'>
                                        <option selected>TODAS</option>
                                        <?php
                                            $SQL = $conn->prepare('SELECT * FROM produtos_um WHERE IDFilial=?');
                                            $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);
                                            $SQL = $SQL->fetchAll();
                                            foreach ($SQL as $value) {
                                                echo "<option value='".$value['UM']."'>".$value['UM']."</option>";
                                            }
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <div class='option'>
                                <h3 class='txtWhite'>SITUAÇÃO</h3>
                                <div class='options'>
                                    <select name="um" class='input ipRound ip100' style='width:100%'>
                                        <option value=''>TODAS</option>
                                        <option value='' selected>ATIVOS</option>
                                        <option value=''>INATIVOS</option>
                                    </select>
                                </div>
                            </div>
                            <input type="submit" class='button btVar btMedium bt100 btRound txtWhite txt700' value='FILTRAR'>
                        </form>
                    </div> -->
                </section>
            </div>

            <!-- SIZE -->
            <div id='modalSize' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Tamanhos</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <div class='size add' onclick="showModal('modalNewSize')">
                                <figure class='fgAbsolute' style='width: 2px; height:21px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 21px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </div>
                            <?php
                                $SQLSize = $conn->prepare("SELECT * FROM produtos_tamanho WHERE IDFilial=?");
                                $SQLSize->execute([$_SESSION['LOGIN']['IDFilial']]);
                                $SQLSize = $SQLSize->fetchAll();
                                foreach ($SQLSize as $value) {
                                    echo "<a href='?IDTamanho=".$value['IDTamanho']."' class='txtNoDecoration'><div class='size'>";
                                        echo "<h2>".$value['Tamanho']."</h2>";
                                    echo "</div></a>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id='modalNewSize' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Criar Tamanho</h1>
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
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeTamanho' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' required>
                                    </label>
                                    <label>
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>TAMANHO</h3>
                                        <input type="text" name='siglaTamanho' class='input ipRound ipBorder ipSmall ip600' placeholder='Sigla' maxlength='3' required>
                                    </label>
                                </div>
                                <input type="submit" name='salvarTamanho' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDTamanho'])) {
                    $SQLSize = $conn->prepare("SELECT * FROM produtos_tamanho WHERE IDTamanho=? LIMIT 1");
                    $SQLSize->execute([$_GET['IDTamanho']]);
                    $SQLSize = $SQLSize->fetch();
                    $nome = $SQLSize['Nome'];
                    $tamanho = $SQLSize['Tamanho'];
            ?>
            <div id='modalDeleteSize' class='modalContainer closeModal mdShow' onclick="location. href='product.php'">
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Excluir Tamanho</h1>
                        <button class='button closeModal' onclick="location. href='product.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <h1 class='txtBlack txtCenter' style='margin-top:10px;'>Você tem certeza?</h1>
                                <h2 class='txtblack txtCenter txt500' style='margin-bottom:15px;'>Deseja excluir o tamanho <b><?php echo $nome;?></b> ?</h2>
                                <center>
                                    <div class='size' style='margin-bottom:15px;'>
                                        <h2 class='txtWhite'><?php echo $tamanho;?></h2>
                                    </div>
                                </center>
                                <input type="submit" name='excluirTamanho' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR TAMANHO'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>
            <!-- COLOR -->
            <div id='modalColor' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Cores</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <div class='color add' onclick="showModal('modalNewColor')">
                                <figure class='fgAbsolute' style='width: 2px; height:21px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 21px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </div>
                            <?php
                                $SQLColor = $conn->prepare("SELECT * FROM produtos_cor WHERE IDFilial=?");
                                $SQLColor->execute([$_SESSION['LOGIN']['IDFilial']]);
                                $SQLColor = $SQLColor->fetchAll();
                                foreach ($SQLColor as $value) {
                                    echo "<a href='?IDCor=".$value['IDCor']."'><div class='color' style='background-color:".$value['Cor']."'></div></a>";
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <div id='modalNewColor' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Criar Cor</h1>
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
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeCor' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' required>
                                    </label>
                                    <label>
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>COR</h3>
                                        <input type="color" name='hexCor' class='input ipRound ipBorder ipSmall ip600' style='height:17px;' required>
                                    </label>
                                </div>
                                <input type="submit" name='salvarCor' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDCor'])) {
                    $SQLSize = $conn->prepare("SELECT * FROM produtos_cor WHERE IDCor=? LIMIT 1");
                    $SQLSize->execute([$_GET['IDCor']]);
                    $SQLSize = $SQLSize->fetch();
                    $nome = $SQLSize['Nome'];
                    $cor = $SQLSize['Cor'];
            ?>
            <div id='modalDeleteSize' class='modalContainer closeModal mdShow' onclick="location. href='product.php'">
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Excluir Cor</h1>
                        <button class='button closeModal' onclick="location. href='product.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <h1 class='txtBlack txtCenter' style='margin-top:10px;'>Você tem certeza?</h1>
                                <h2 class='txtblack txtCenter txt500' style='margin-bottom:15px;'>Deseja excluir a cor <b><?php echo $nome;?></b>?</h2>
                                <center><div style='margin-bottom:15px;width: 60px; height: 60px; border:1px solid #000; border-radius:100%; background-color:<?php echo $cor;?>'></div></center>
                                <input type="submit" name='excluirCor' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR COR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>

            <!-- CATEGORY -->
            <div id='modalCategory' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Categoria</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <center>
                            <div class="divFlex" style='display:inherit;'>
                                <div class='category add' onclick="showModal('modalNewCategory')">
                                    <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                        <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                        <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                                    </figure>
                                    <h3 class='txt600'>CATEGORIA</h3>
                                </div>
                                <?php
                                    $SQLCategory = $conn->prepare("SELECT * FROM produtos_categoria WHERE IDFilial=?");
                                    $SQLCategory->execute([$_SESSION['LOGIN']['IDFilial']]);
                                    $SQLCategory = $SQLCategory->fetchAll();
                                    foreach ($SQLCategory as $value) {
                                        echo "<a class='txtNoDecoration' href='?IDCategoria=".$value['IDCategoria']."'>";
                                            echo "<div class='category'>";
                                                echo "<h3 class='txtWhite txtUpper'>".$value['Nome']."</h3>";
                                            echo "</div>";
                                        echo "</a>";
                                    }
                                ?>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
            <div id='modalNewCategory' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Criar Categoria</h1>
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
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeCategoria' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' required>
                                        <label>
                                            <input type="checkbox" name='menuCategoria' style='margin-left:10px;'>
                                            Menu
                                        </label>
                                    </label>
                                </div>
                                <input type="submit" name='salvarCategoria' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDCategoria'])) {
                    $SQLCategory = $conn->prepare("SELECT * FROM produtos_categoria WHERE IDCategoria=? LIMIT 1");
                    $SQLCategory->execute([$_GET['IDCategoria']]);
                    $SQLCategory = $SQLCategory->fetch();
                    $nome = $SQLCategory['Nome'];
                    $menu = '';
                    if ($SQLCategory['Menu'] == '1')
                        $menu = 'checked';
            ?>
            <div id='modalDeleteCategory' class='modalContainer closeModal mdShow'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Alterar Categoria</h1>
                        <button class='button closeModal' onclick="location. href='product.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <div style='display:flex; justify-content:center; margin-bottom:13px;'>
                                    <label style='margin-right: 13px'>
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeCategoria' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' value='<?php echo $nome; ?>' required>
                                        <label>
                                            <input type="checkbox" name='menuCategoria' style='margin-left:10px;' <?php echo $menu; ?>>
                                            Menu
                                        </label>
                                    </label>
                                </div>
                                <input type="submit" name='alterarCategoria' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR CATEGORIA'>
                            </form>
                            <form method='post' style='width:100%;margin-top:10px;'>
                                <input type="submit" name='excluirCategoria' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR CATEGORIA'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>

            <!-- BRAND -->
            <div id='modalBrand' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Marca</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <center>
                            <div class="divFlex" style='display:inherit;'>
                                <div class='category add' onclick="showModal('modalNewBrand')">
                                    <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                        <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                        <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                                    </figure>
                                    <h3 class='txt600'>MARCA</h3>
                                </div>
                                <?php
                                    $SQLBrand = $conn->prepare("SELECT * FROM produtos_marca WHERE IDFilial=?");
                                    $SQLBrand->execute([$_SESSION['LOGIN']['IDFilial']]);
                                    $SQLBrand = $SQLBrand->fetchAll();
                                    foreach ($SQLBrand as $value) {
                                        echo "<a class='txtNoDecoration' href='?IDMarca=".$value['IDMarca']."'>";
                                            echo "<div class='category'>";
                                                echo "<h3 class='txtWhite txtUpper'>".$value['Nome']."</h3>";
                                            echo "</div>";
                                        echo "</a>";
                                    }
                                ?>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
            <div id='modalNewBrand' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Criar Marca</h1>
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
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeMarca' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' required>
                                    </label>
                                </div>
                                <input type="submit" name='salvarMarca' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDMarca'])) {
                    $SQLBrand = $conn->prepare("SELECT * FROM produtos_marca WHERE IDMarca=? LIMIT 1");
                    $SQLBrand->execute([$_GET['IDMarca']]);
                    $SQLBrand = $SQLBrand->fetch();
                    $nome = $SQLBrand['Nome'];
            ?>
            <div id='modalDeleteCategory' class='modalContainer closeModal mdShow' onclick="location. href='product.php'">
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Excluir Marca</h1>
                        <button class='button closeModal' onclick="location. href='product.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <h1 class='txtBlack txtCenter' style='margin-top:10px;'>Você tem certeza?</h1>
                                <h2 class='txtblack txtCenter txt500' style='margin-bottom:15px;'>Deseja excluir a marca <b><?php echo $nome;?></b>?</h2>
                                <input type="submit" name='excluirMarca' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR MARCA'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                }
            ?>

            <!-- UM -->
            <div id='modalUM' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Unidade de Medida</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <center>
                            <div class="divFlex" style='display:inherit;'>
                                <div class='category add' onclick="showModal('modalNewUM')">
                                    <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                        <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                        <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                                    </figure>
                                    <h3 class='txt600'>UM</h3>
                                </div>
                                <?php
                                    $SQLBrand = $conn->prepare("SELECT * FROM produtos_um WHERE IDFilial=?");
                                    $SQLBrand->execute([$_SESSION['LOGIN']['IDFilial']]);
                                    $SQLBrand = $SQLBrand->fetchAll();
                                    foreach ($SQLBrand as $value) {
                                        echo "<a class='txtNoDecoration' href='?IDUM=".$value['IDUM']."'>";
                                            echo "<div class='category'>";
                                                echo "<h3 class='txtWhite txtUpper'>".$value['Nome']."</h3>";
                                            echo "</div>";
                                        echo "</a>";
                                    }
                                ?>
                            </div>
                        </center>
                    </div>
                </div>
            </div>
            <div id='modalNewUM' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Criar Unidade de Medida</h1>
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
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>SIGLA</h3>
                                        <input type="text" name='siglaUM' class='input ipRound ipBorder ipSmall ip600' maxlength='3' required>
                                    </label>
                                    <label style='margin-right: 13px'>
                                        <h3 class='txtBlack' style='margin-bottom:7px;'>NOME</h3>
                                        <input type="text" name='nomeUM' class='input ipRound ipBorder ipBigger ip600' placeholder='Digite aqui o nome' required>
                                    </label>
                                </div>
                                <input type="submit" name='salvarUM' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='SALVAR'>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php
                if (isset($_GET['IDUM'])) {
                    $SQLUM = $conn->prepare("SELECT * FROM produtos_um WHERE IDUM=? LIMIT 1");
                    $SQLUM->execute([$_GET['IDUM']]);
                    $SQLUM = $SQLUM->fetch();
                    $nome = $SQLUM['Nome'];
            ?>
            <div id='modalDeleteCategory' class='modalContainer closeModal mdShow' onclick="location. href='product.php'">
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Excluir Unidade de Medida</h1>
                        <button class='button closeModal' onclick="location. href='product.php'">
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class="divFlex" style='width:100%;justify-content:center;'>
                            <form method='post' style='width:100%;'>
                                <h1 class='txtBlack txtCenter' style='margin-top:10px;'>Você tem certeza?</h1>
                                <h2 class='txtblack txtCenter txt500' style='margin-bottom:15px;'>Deseja excluir a Unidade de Medida <b><?php echo $nome;?></b>?</h2>
                                <input type="submit" name='excluirUM' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='EXCLUIR UM'>
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