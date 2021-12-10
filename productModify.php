<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");

    if (!isset($_GET['ID'])) {
        $required = 'required';

        $SQL = $conn->prepare('SELECT * FROM produtos WHERE Situacao=1 AND IDFilial=? ORDER BY IDProdutoFilial DESC LIMIT 1');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);
        
        $codigo = 1;
        if ($SQL->rowCount() > 0) {
            $SQL = $SQL->fetch();
            if ($SQL['IDProdutoFilial'] <> '')
                $codigo = $SQL['IDProdutoFilial'] + 1;
        }

        $img1 = '';
        $img2 = '';
        $img3 = '';
        $img4 = '';   
        
        $ultimaVenda = '';

        $nome = '';
        $marca = '';
        $categoria = '';
        $subcategoria = '';

        $um = '';
        $altura = '';
        $largura = '';
        $peso = '';
        $comprimento = '';

        $valorVenda = '';
        $valorCusto = '0,00';
        $markup = '50%';
        $parcelas = 1;
        $descricao = '';
    }

    if (isset($_GET['ID'])) {
        $required = '';

        $SQL = $conn->prepare('SELECT * FROM produtos WHERE IDProduto=? LIMIT 1');
        $SQL->execute([$_GET['ID']]);
        $SQL = $SQL->fetch(); 

        $img1 = IMAGE . $SQL['Imagem'];
        $img2 = IMAGE . $SQL['Imagem2'];
        $img3 = IMAGE . $SQL['Imagem3'];
        $img4 = IMAGE . $SQL['Imagem4'];

        $codigo = $SQL['Codigo'];
        $ultimaVenda = $SQL['UltimaVenda'];
        if ($ultimaVenda == '')
            $ultimaVenda = 'Sem venda';

        $nome = $SQL['Nome'];

        $marca = $SQL['IDMarca'];
        $SQLBrand = $conn->prepare('SELECT * FROM produtos_marca WHERE IDMarca=? LIMIT 1');
        $SQLBrand->execute([$marca]);
        $SQLBrand = $SQLBrand->fetch();
        $nomeMarca = $SQLBrand['Nome'];

        $categoria = $SQL['IDCategoria'];
        $SQLCategory = $conn->prepare('SELECT * FROM produtos_categoria WHERE IDCategoria=? LIMIT 1');
        $SQLCategory->execute([$categoria]);
        $SQLCategory = $SQLCategory->fetch();
        $nomeCategoria = $SQLCategory['Nome'];

        $um = $SQL['UM'];
        $altura = $SQL['Altura'];
        $largura = $SQL['Largura'];
        $peso = $SQL['Peso'];
        $comprimento = $SQL['Comprimento'];
        $valorVenda = $SQL['ValorVenda'];
        $valorCusto = $SQL['ValorCusto'];
        $markup = $SQL['Markup'];
        $parcelas = $SQL['Parcelas'];
        $descricao = $SQL['Descricao'];
        $situacao = $SQL['Situacao'];
    }

    if ((isset($_POST['salvar'])) and (isset($_GET['ID'])) ) {
        for ($i = 1; $i <= 4; $i++) {
            if ($_FILES['img'.$i]['size'] > 0) {
                $destino = IMAGE . $_FILES['img'.$i]['name'];
                $arquivo_tmp = $_FILES['img'.$i]['tmp_name'];
                move_uploaded_file($arquivo_tmp, $destino);
            }
        }

        if ($_FILES['img1']['name'] == '') 
            $_FILES['img1']['name'] = $SQL['Imagem'];
        
        if ($_FILES['img2']['name'] == '')
            $_FILES['img2']['name'] = $SQL['Imagem2'];
        
        if ($_FILES['img3']['name'] == '')
            $_FILES['img3']['name'] = $SQL['Imagem3'];

        if ($_FILES['img4']['name'] == '')
            $_FILES['img4']['name'] = $SQL['Imagem4'];
        
        $valorVenda = str_replace(',', '.', $_POST['valorVenda']);
        $valorCusto = str_replace(',', '.', $_POST['valorCusto']);
        // $markup = '0';
        // if (isset($_POST['markup']))
        //     $markup = str_replace(',', '.', $_POST['markup']);
        
        $altura = str_replace(',', '.', $_POST['altura']);
        $largura = str_replace(',', '.', $_POST['largura']);
        $peso = str_replace(',', '.', $_POST['peso']);
        $comprimento = str_replace(',', '.', $_POST['comprimento']);

        try {
            $SQL = $conn->prepare('UPDATE produtos SET IDMarca=?, IDCategoria=?, Imagem=?, Imagem2=?, Imagem3=?, Imagem4=?, Codigo=?,
                                   Nome=?, ValorVenda=?, ValorCusto=?, Parcelas=?, Descricao=?, UM=?, Altura=?, Largura=?, Peso=?, Comprimento=? 
                                   WHERE IDProduto=? LIMIT 1');
            $SQL->execute([ $_POST['marca'], $_POST['categoria'], $_FILES['img1']['name'], $_FILES['img2']['name'],
                            $_FILES['img3']['name'], $_FILES['img4']['name'], $_POST['codigo'], $_POST['nome'], $valorVenda, $valorCusto, 
                            $_POST['parcelas'], $_POST['descricao'], $_POST['um'], $altura, $largura, $peso, $comprimento,
                            $_GET['ID'] ]);

            $SQLStock = $conn->prepare('SELECT IDCor, IDTamanho, Cor, Tamanho FROM produtos_cor a INNER JOIN produtos_tamanho b ON (a.IDFilial = b.IDFilial)
                                        WHERE a.IDFilial=? ORDER BY a.IDCor, b.IDTamanho');
            $SQLStock->execute([$_SESSION['LOGIN']['IDFilial']]);
            $SQLStock = $SQLStock->fetchAll();
            foreach ($SQLStock as $value) {
                $SQLVerify = $conn->prepare('SELECT * FROM produtos_cor_tamanho WHERE IDCor=? AND IDTamanho=? AND IDProduto=? LIMIT 1');
                $SQLVerify->execute([$value['IDCor'], $value['IDTamanho'], $_GET['ID']]);

                if ($SQLVerify->rowCount() > 0) {
                    $SQLColorSize = $conn->prepare('UPDATE produtos_cor_tamanho SET Estoque=? WHERE IDCor=? AND IDTamanho=? AND IDProduto=? LIMIT 1');
                    $SQLColorSize->execute([ $_POST['color' . $value['IDCor'] . 'Size' . $value['IDTamanho']], $value['IDCor'], $value['IDTamanho'], $_GET['ID'] ]);
                } else {
                    $SQLColorSize = $conn->prepare('INSERT INTO produtos_cor_tamanho (IDProduto, IDCor, IDTamanho, Estoque) VALUES (?, ?, ?, ?)');
                    $SQLColorSize->execute([$_GET['ID'], $value['IDCor'], $value['IDTamanho'], $_POST['color' . $value['IDCor'] . 'Size' . $value['IDTamanho']] ]);
                }
            }

            header("location: product.php");
        } catch(PDOException $e) {
            echo "<div class='message msRed'>";
                echo '<h2>'.$e.'</h2>';
            echo "</div>";
        }
    }

    if ((isset($_POST['salvar'])) and (!isset($_GET['ID'])) ) {
        if (!is_dir(IMAGE)) 
            mkdir(IMAGE, 777, true);

        for ($i = 1; $i <= 4; $i++) {
            if ($_FILES['img'.$i]['size'] > 0) {
                $destino = IMAGE . $_FILES['img'.$i]['name'];
                $arquivo_tmp = $_FILES['img'.$i]['tmp_name'];
                move_uploaded_file($arquivo_tmp, $destino);
            }
        }

        $SQL = $conn->prepare('SELECT * FROM produtos WHERE Situacao=1 AND IDFilial=? ORDER BY IDProdutoFilial DESC LIMIT 1');
        $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);

        $IDProdutoFilial = 1;
        if ($SQL->rowCount() > 0) {
            $SQL = $SQL->fetch();
            if ($SQL['IDProdutoFilial'] <> '')
                $IDProdutoFilial = $SQL['IDProdutoFilial'] + 1;
        }

        $valorVenda = str_replace(',', '.', $_POST['valorVenda']);
        $valorCusto = str_replace(',', '.', $_POST['valorCusto']);
        $markup = '0';
        if (isset($_POST['markup']))
            $markup = str_replace(',', '.', $_POST['markup']);
        
        $altura = str_replace(',', '.', $_POST['altura']);
        $largura = str_replace(',', '.', $_POST['largura']);
        $peso = str_replace(',', '.', $_POST['peso']);
        $comprimento = str_replace(',', '.', $_POST['comprimento']);

        try {
            $SQL = $conn->prepare('INSERT INTO produtos (IDProdutoFilial,
                                IDFilial, IDUsuarioCriacao, IDMarca, IDCategoria, IDSubCategoria, Imagem, Imagem2, Imagem3, Imagem4, Codigo,
                                Nome, ValorVenda, ValorCusto, Markup, Parcelas, Descricao, UM, Altura, Largura, Peso, Comprimento,
                                DataCadastro, Situacao) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                                                                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?,
                                                                ?, ?, ?)');
            $SQL->execute([ $IDProdutoFilial,
                            $_SESSION['LOGIN']['IDFilial'], $_SESSION['LOGIN']['ID'], $_POST['marca'], $_POST['categoria'], 0,
                            $_FILES['img1']['name'], $_FILES['img2']['name'], $_FILES['img3']['name'], $_FILES['img4']['name'], $_POST['codigo'],
                            $_POST['nome'], $valorVenda, $valorCusto, $markup, $_POST['parcelas'], $_POST['descricao'],
                            $_POST['um'], $altura, $largura, $peso, $comprimento,
                            date('Y/m/d'), '1' ]);
                            
            if ($SQL->rowCount() > 0){
                $SQL = $conn->prepare('SELECT * FROM produtos WHERE Situacao=1 AND IDFilial=? ORDER BY IDProduto DESC LIMIT 1');
                $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);
                $SQL = $SQL->fetch();

                $SQLStock = $conn->prepare('SELECT IDCor, IDTamanho, Cor, Tamanho FROM produtos_cor a INNER JOIN produtos_tamanho b ON (a.IDFilial = b.IDFilial)
                                            WHERE a.IDFilial=? ORDER BY a.IDCor, b.IDTamanho');
                $SQLStock->execute([$_SESSION['LOGIN']['IDFilial']]);
                $SQLStock = $SQLStock->fetchAll();
                foreach ($SQLStock as $value) {
                    $SQLColorSize = $conn->prepare('INSERT INTO produtos_cor_tamanho (IDProduto, IDCor, IDTamanho, Estoque) VALUES (?, ?, ?, ?)');
                    $SQLColorSize->execute([$SQL['IDProduto'], $value['IDCor'], $value['IDTamanho'], $_POST['color' . $value['IDCor'] . 'Size' . $value['IDTamanho']] ]);
                }

                echo "<div class='message msGreen'>";
                    echo '<h2>Produto criado com sucesso!</h2>';
                echo "</div>";
            }
        } catch (PDOException $e) {
            echo "<div class='message msRed'>";
                echo '<h2>'.$e.'</h2>';
            echo "</div>";
        }
    }

    if (isset($_POST['inative'])) {
        $SQL = $conn->prepare('UPDATE produtos SET Situacao=? WHERE IDProduto=? LIMIT 1');
        $SQL->execute(['2', $_GET['ID']]);

        header("location: productModify.php?ID=".$_GET['ID']);
    }

    if (isset($_POST['ative'])) {
        $SQL = $conn->prepare('UPDATE produtos SET Situacao=? WHERE IDProduto=? LIMIT 1');
        $SQL->execute(['1', $_GET['ID']]);

        header("location: productModify.php?ID=".$_GET['ID']);
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
            <h2 class='txtPages txtUpper txt400'><a href="dashboard.php" class='txtPages txtNoDecoration'>Painel</a> / 
                                                 <a href="product.php" class='txtPages txtNoDecoration'>Produtos</a> / Cadastro de Produto</h2>
            <h1 class='txtTitle txt500'>Cadastro de Produto</h1>
            <div class='divFlex' style='margin-top:20px'>
                <form method='post' enctype="multipart/form-data">
                    <div id='images'>
                        <div id='img1'>
                            <!-- <input type="hidden" name="MAX_FILE_SIZE" value="1048576" /> -->
                            <img src="<?php echo $img1; ?>" id='imgPreview1' alt="">
                            <input type="file" name='img1' id='imgFile1' class='input ipHidden' accept='.jpg, .png, .jpeg' <?php echo $required; ?>>
                            <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </figure>
                            <h3 class='txt600 txtGrey'>IMAGEM PRINCIPAL</h3>
                        </div>
                        <div class='img2'>
                            <img src="<?php echo $img2; ?>" id='imgPreview2' alt="">
                            <input type="file" name='img2' id='imgFile2' class='input ipHidden' accept='.jpg, .png, .jpeg'>
                            <input type="button" id='imgDel2' class='button txtWhite txt600' value='Apagar'>
                            <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </figure>
                            <h3 class='txt600 txtGrey'>IMAGEM 2</h3>
                        </div>
                        <div class='img2'>
                            <img src="<?php echo $img3; ?>" id='imgPreview3' alt="">
                            <input type="file" name='img3' id='imgFile3' class='input ipHidden' accept='.jpg, .png, .jpeg'>
                            <input type="button" id='imgDel3' class='button txtWhite txt600' value='Apagar'>
                            <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </figure>
                            <h3 class='txt600 txtGrey'>IMAGEM 3</h3>
                        </div>
                        <div class='img2'>
                            <img src="<?php echo $img4; ?>" id='imgPreview4' alt="">
                            <input type="file" name='img4' id='imgFile4' class='input ipHidden' accept='.jpg, .png, .jpeg'>
                            <input type="button" id='imgDel4' class='button txtWhite txt600' value='Apagar'>
                            <figure class='fgRounded' style='width:24px;height:24px; margin-right:9px;'>
                                <figure class='fgAbsolute' style='width: 2px; height:11px; background-color: var(--product-modify-icon);'></figure>
                                <figure class='fgAbsolute' style='width: 11px; height:2px; background-color: var(--product-modify-icon);'></figure>
                            </figure>
                            <h3 class='txt600 txtGrey'>IMAGEM 4</h3>
                        </div>
                    </div>
                    <div id='register' style='margin-left: 30px;'>
                        <div class='divFlex'>
                            <label>
                                <h3>CÓDIGO</h3>
                                <input type="text" name='codigo' class='input ipRound ipBig ipGreen txtWhite txt600' placeholder='Código de Referência' value='<?php echo $codigo; ?>' required>
                            </label>
                            <label>
                                <h3>ÚLTIMA VENDA</h3>
                                <input type="text" class='input ipRound ipNormal txtCenter txt600' value='<?php echo $ultimaVenda; ?>' disabled>
                            </label>
                        </div>
                        <div class='divFlex'>
                            <label>
                                <h3>NOME</h3>
                                <input type="text" name='nome' class='input ipRound ipBigger txt600' placeholder='Nome que aparecerá na vitrine' value='<?php echo $nome; ?>' required>
                            </label>
                            <label>
                                <h3>MARCA</h3>
                                <select name="marca" class='input ipRound ipNormal txt600 txtUpper' required>
                                    <?php
                                        if (isset($_GET['ID']))
                                            echo "<option value='".$marca."'>".$nomeMarca."</option>";

                                        $SQL = $conn->prepare('SELECT * FROM produtos_marca WHERE IDFilial=? AND IDMarca<>?');
                                        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $marca]);
                                        $SQL = $SQL->fetchAll();
                                        foreach ($SQL as $value) {
                                            echo "<option value='".$value['IDMarca']."'>".$value['Nome']."</option>";
                                        }
                                    ?>
                                </select>
                            </label>
                            <label>
                                <h3>CATEGORIA</h3>
                                <select name="categoria" class='input ipRound ipNormal txt600 txtUpper' required>
                                    <?php
                                        if (isset($_GET['ID']))
                                            echo "<option value='".$categoria."'>".$nomeCategoria."</option>";

                                        $SQL = $conn->prepare('SELECT * FROM produtos_categoria WHERE IDFilial=? AND IDCategoria<>?');
                                        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $categoria]);
                                        $SQL = $SQL->fetchAll();
                                        foreach ($SQL as $value) {
                                            echo "<option value='".$value['IDCategoria']."'>".$value['Nome']."</option>";
                                        }

                                    ?>
                                </select>
                            </label>
                            <!-- <label>
                                <h3>&nbsp;</h3>
                                <input type="hidden" name='categoria' value='0'>
                                <input type="hidden" name='subcategoria' value='0'>
                                <input type='button' class='button btVar btRound btNormal txtWhite txt600 txtUpper' value='SELECIONAR CATEGORIA'>
                            </label> -->
                        </div>
                        <div class='divFlex'>
                            <label>
                                <h3>UM</h3>
                                <select name="um" class='input ipRound ipSmall txt600 txtUpper' required>
                                    <?php
                                        if (isset($_GET['ID']))
                                            echo "<option value='".$um."'>".$um."</option>";

                                        $SQL = $conn->prepare('SELECT * FROM produtos_um WHERE IDFilial=? AND UM<>?');
                                        $SQL->execute([$_SESSION['LOGIN']['IDFilial'], $um]);
                                        $SQL = $SQL->fetchAll();
                                        foreach ($SQL as $value) {
                                            echo "<option value='".$value['UM']."'>".$value['UM']."</option>";
                                        }
                                    ?>
                                </select>
                            </label>
                            <label>
                                <h3>ALTURA</h3>
                                <input type="text" name='altura' class='input ipRound ipSmall txt600' value='<?php echo $altura; ?>' onkeypress="return onlynumber();" placeholder='0,00' required>
                            </label>
                            <label>
                                <h3>LARGURA</h3>
                                <input type="text" name='largura' class='input ipRound ipSmall txt600' value='<?php echo $largura; ?>' onkeypress="return onlynumber();" placeholder='0,00' required>
                            </label>
                            <label>
                                <h3>PESO</h3>
                                <input type="text" name='peso' class='input ipRound ipSmall txt600' value='<?php echo $peso; ?>' onkeypress="return onlynumber();"  placeholder='0,00'required>
                            </label>
                            <label>
                                <h3>COMPRIMENTO</h3>
                                <input type="text" name='comprimento' class='input ipRound ipSmall txt600' value='<?php echo $comprimento; ?>' onkeypress="return onlynumber();" placeholder='0,00' required>
                            </label>
                            <label>
                                <h3>&nbsp;</h3>
                                <input type='button' onclick="showModal('modalCoresTamanhos')" class='button btPink btRound btNormal txtWhite txt600 txtUpper' value='CORES E TAMANHOS'>
                            </label>
                            <div id='modalCoresTamanhos' class='modalContainer closeModal'>
                            <div class="modal">
                                <div class="mdTitle mdTitleBlue">
                                    <h1 class='txtUpper txtWhite txt600'>CORES E TAMANHOS</h1>
                                    <button class='button closeModal' onclick=''>
                                        <div class='closeModal x1'></div>
                                        <div class='closeModal x2'></div>
                                    </button>
                                </div>
                                <div class='mainModal'>
                                    <div class='center' style='height:250px;margin-top:-20px;margin-bottom:0px;overflow:auto'>
                                        <?php
                                            $SQLStock = $conn->prepare('SELECT IDCor, IDTamanho, Cor, Tamanho FROM produtos_cor a INNER JOIN produtos_tamanho b ON (a.IDFilial = b.IDFilial)
                                                                        WHERE a.IDFilial=? ORDER BY a.IDCor, b.IDTamanho');
                                            $SQLStock->execute([$_SESSION['LOGIN']['IDFilial']]);
                                            $SQLStock = $SQLStock->fetchAll();
                                            foreach ($SQLStock as $value) {
                                                echo "<div style='margin:auto;display:flex;margin-left:80px;margin-bottom:10px;'>";
                                                    echo "<div style='width:140px;height:45px;margin-right:10px;border:1px solid #000;border-radius:7px;background-color:" . $value['Cor'] . "'></div>";
                                                    echo "<div style='width:140px;height:45px;margin-right:10px;border-radius:7px;background-color:#2B2C30;display:flex;justify-content:center;align-items:center;'>";
                                                        echo "<h3 class='txtWhite'>" . $value['Tamanho'] . "</h3>";
                                                    echo "</div>";

                                                    $estoque = 0;

                                                    if (isset($_GET['ID'])) {
                                                        $SQLColorSize = $conn->prepare('SELECT * FROM produtos_cor_tamanho WHERE IDCor=? AND IDTamanho=? AND IDProduto=? LIMIT 1');
                                                        $SQLColorSize->execute([$value['IDCor'], $value['IDTamanho'], $_GET['ID']]);

                                                        if ($SQLColorSize->rowCount() > 0) {
                                                            $SQLColorSize = $SQLColorSize->fetch();
                                                            $estoque = $SQLColorSize['Estoque'];
                                                        }
                                                    }

                                                    echo "<input type='text' name='color" . $value['IDCor'] . "Size" . $value['IDTamanho'] . "' class='input ipRound ipBig ipPurple txtWhite txt600 txtCenter' value='" . $estoque . "' style='width:140px;font-size:20px' onkeypress='return onlynumber();'>";
                                                echo "</div>";
                                            }
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        </div>
                        <div class='divFlex'>
                            <label>
                                <h3>VALOR VENDA</h3>
                                <input type="text" name='valorVenda' class='input ipRound ipNormal ipDarkGreen txtWhite txtCenter txt600' placeholder='0.00' value='<?php echo $valorVenda; ?>' onkeypress="return onlynumber();" required>
                            </label>
                            <label>
                                <h3>VALOR CUSTO</h3>
                                <input type="text" name='valorCusto' class='input ipRound ipNormal ipYellow txtWhite txtCenter txt600' value='<?php echo $valorCusto; ?>' onkeypress="return onlynumber();">
                            </label>
                            <!-- <label>
                                <h3>&nbsp;</h3>
                                <input type="checkbox" name='utilizar' class='input' style='margin-top: 12px'> <b>Utilizar</b>
                            </label>
                            <label>
                                <h3>MARKUP</h3>
                                <input type="text" name='markup' class='input ipRound ipSmall txtCenter txt600' value='<?php echo $markup; ?>' onkeypress="return onlynumber();" disabled>
                            </label> -->
                            <label>
                                <h3>PARCELAS</h3>
                                <input type="text" name='parcelas' class='input ipRound ipSmall txtCenter txt600' value='<?php echo $parcelas; ?>' onkeypress="return onlynumber();">
                            </label>
                        </div>
                        <div class='divFlex'>
                            <label style='width:100%;'>
                                <h3>DESCRIÇÃO</h3>
                                <textarea name="descricao" class='input ipRound txt600' rows="13" placeholder='Mais detalhes do produto' style='padding:10px; width:98%;'><?php echo $descricao; ?></textarea>
                            </label>
                        </div>
                        <div class='divFlex'>
                            <label style='width:100%'>
                                <input type="submit" name='salvar' class='button bt100 btRound btVar txtWhite txt600' value='SALVAR PRODUTO' style='height:50px; font-size:15px;'>
                            </label>
                        </div>
                        <?php
                            if (isset($_GET['ID'])) {
                                if ($situacao == 1) {
                        ?>
                        <div class='divFlex'>
                            <label style='width:100%'>
                                <input type="button" name='inativar' onclick="showModal('modalInative')" class='button bt100 btRound btRed txtWhite txt600' value='INATIVAR PRODUTO' style='height:50px; font-size:15px'>
                            </label>
                        </div>
                        <?php
                            } else {
                        ?>
                        <div class='divFlex'>
                            <label style='width:100%'>
                                <input type="button" name='ativar' onclick="showModal('modalAtive')" class='button bt100 btRound btVar txtWhite txt600' value='ATIVAR PRODUTO' style='height:50px; font-size:15px'>
                            </label>
                        </div>
                        <?php }} ?>
                    </div>
                </form>
            </div>
            <div id='modalMarca' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Marcas</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <button class='button btVar bt100 btRound txtWhite txt600 btModalSelect'>SELECIONAR</button>
                    </div>
                </div>
            </div>
            <div id='modalAtive' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Ativar Produto</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>Deseja ativar o produto <b><?php echo $nome;?></b>?</br>
                                                                  Ele será mostrado na vitrine!</h2>
                        </div>
                        <form method='post'>
                            <input type="submit" name='ative' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='ATIVAR PRODUTO'>
                        </form>
                    </div>
                </div>
            </div>
            <div id='modalInative' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Inativar Produto</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>Deseja inativar o produto <b><?php echo $nome;?></b>?</br>
                                                                  Ele desaparecerá da vitrine!</h2>
                        </div>
                        <form method='post'>
                            <input type="submit" name='inative' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='INATIVAR PRODUTO'>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <script src='JS/mask.js'></script>
        <script>
            const $ = document.querySelector.bind(document);
            var previewImg = $('#imgPreview1');
            var fileChooser = $('#imgFile1');

            fileChooser.onchange = e => {
                const fileToUpload = e.target.files.item(0);
                const reader = new FileReader();

                // evento disparado quando o reader terminar de ler 
                reader.onload = e => previewImg.src = e.target.result;

                // solicita ao reader que leia o arquivo 
                // transformando-o para DataURL. 
                // Isso disparará o evento reader.onload.
                reader.readAsDataURL(fileToUpload);
            };

            var previewImg2 = $('#imgPreview2');
            var fileChooser2 = $('#imgFile2');
            var delImg2 = $('#imgDel2');

            delImg2.onclick = e => {
                previewImg2.src = '';
                fileChooser2.value = '';
            }

            fileChooser2.onchange = e => {
                const fileToUpload = e.target.files.item(0);
                const reader = new FileReader();

                reader.onload = e => previewImg2.src = e.target.result;
                reader.readAsDataURL(fileToUpload);
            };

            var previewImg3 = $('#imgPreview3');
            var fileChooser3 = $('#imgFile3');
            var delImg3 = $('#imgDel3');

            delImg3.onclick = e => {
                previewImg3.src = '';
                fileChooser3.value = '';
            }

            fileChooser3.onchange = e => {
                const fileToUpload = e.target.files.item(0);
                const reader = new FileReader();

                reader.onload = e => previewImg3.src = e.target.result;
                reader.readAsDataURL(fileToUpload);
            };

            var previewImg4 = $('#imgPreview4');
            var fileChooser4 = $('#imgFile4');
            var delImg4 = $('#imgDel4');

            delImg4.onclick = e => {
                previewImg4.src = '';
                fileChooser4.value = '';
            }

            fileChooser4.onchange = e => {
                const fileToUpload = e.target.files.item(0);
                const reader = new FileReader();

                reader.onload = e => previewImg4.src = e.target.result;
                reader.readAsDataURL(fileToUpload);
            };
            
        </script>
    </body>
</html>