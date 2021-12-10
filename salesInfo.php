<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");

    if (isset($_GET['ID'])) {
        $SQL = $conn->prepare('SELECT * FROM pedido WHERE IDPedido=? LIMIT 1');
        $SQL->execute([$_GET['ID']]);
        $SQL = $SQL->fetch();

        $codigo = $SQL['IDPedido'];
        $valorLiquido = 'R$ ' . $SQL['ValorLiquido'];

        switch ($SQL['Situacao']) {
            case 1:
                $situacao = 'AGENDADO PARA ENTREGA EM <b>' . $SQL['DataEntrega'] . '</b> ÀS <b>' . $SQL['HoraEntrega'] . '</b>';
                $botoes = "<button onclick=".'"showModal('."'modalCancel'".')'.'"'." class='button btRound btRed btMedium txtWhite txt600'>CANCELAR</button>
                           <button onclick=".'"showModal('."'modalDespatch'".')'.'"'." class='button btRound btVar btMedium txtWhite txt600'>DESPACHAR</button>";
                break;
            case 2:
                $situacao = 'AGENDADO PARA ENTREGA EM <b>' . $SQL['DataEntrega'] . '</b> ÀS <b>' . $SQL['HoraEntrega'] . '</b>';
                $botoes = "<button onclick=".'"showModal('."'modalCancel'".')'.'"'." class='button btRound btRed btMedium txtWhite txt600'>CANCELAR</button>
                           <button onclick=".'"showModal('."'modalDespatch'".')'.'"'." class='button btRound btVar btMedium txtWhite txt600'>DESPACHAR</button>";
                break;
            case 3:
                $situacao = 'ENVIADO PARA ENTREGA EM <b>' . $SQL['DataEnvio'] . '</b> ÀS <b>' . $SQL['HoraEnvio'] . '</b>';
                $botoes = "<button onclick=".'"showModal('."'modalCancel'".')'.'"'." class='button btRound btRed btMedium txtWhite txt600'>CANCELAR</button>
                           <button onclick=".'"showModal('."'modalDelivered'".')'.'"'." class='button btRound btVar btMedium txtWhite txt600'>ENTREGUE</button>";
                break;
            case 4:
                $situacao = 'CANCELADO EM <b>' . $SQL['DataCancelamento'] . '</b> ÀS <b>' . $SQL['HoraCancelamento'] . '</b>';
                $botoes = "<button onclick=".'"showModal('."'modalRelease'".')'.'"'." class='button btRound btVar btMedium txtWhite txt600' style='width:390px;'>LIBERAR</button>";
                break;
            case 5:
                $situacao = 'ENTREGUE EM <b>' . $SQL['DataEntrega'] . '</b> ÀS <b>' . $SQL['HoraEntrega'] . '</b>';
                $botoes = "<button onclick=".'"showModal('."'modalCancel'".')'.'"'." class='button btRound btRed btMedium txtWhite txt600' style='width:390px;'>CANCELAR</button>";
                break;
        }
        
        $dataCriacao = $SQL['DataCriacao'] . ' ' . $SQL['HoraCriacao'];
        $item = $SQL['Volume'];
        $valorFrete = 'R$ ' . $SQL['ValorFrete'];

        $SQLEntrega = $conn->prepare('SELECT * FROM pedido_entrega WHERE IDPedido=? LIMIT 1');
        $SQLEntrega->execute([$codigo]);
        $SQLEntrega = $SQLEntrega->fetch();

        $formaEntrega = $SQLEntrega['Forma'];
        $enderecoEntrega = $SQLEntrega['Endereco'];
        $bairroEntrega = $SQLEntrega['Bairro'];
        $cepEntrega = $SQLEntrega['CEP'];
        $municipioEntrega = $SQLEntrega['Municipio'];
        $ufEntrega = $SQLEntrega['UF'];
        $paisEntrega = $SQLEntrega['Pais'];

        $SQLCobranca = $conn->prepare('SELECT * FROM pedido_cobranca WHERE IDPedido=? LIMIT 1');
        $SQLCobranca->execute([$codigo]);
        $SQLCobranca = $SQLCobranca->fetch();

        $formaCobranca = $SQLCobranca['Forma'];
        $enderecoCobranca = $SQLCobranca['Endereco'];
        $bairroCobranca = $SQLCobranca['Bairro'];
        $cepCobranca = $SQLCobranca['CEP'];
        $municipioCobranca = $SQLCobranca['Municipio'];
        $ufCobranca = $SQLCobranca['UF'];
        $paisCobranca = $SQLCobranca['Pais'];
    }

    if (isset($_POST['releaseSale'])) {
        $SQL = $conn->prepare('UPDATE pedido SET Situacao=? WHERE IDPedido=?');
        $SQL->execute(['2', $_GET['ID']]);

        header("location: salesInfo.php?ID=".$_GET['ID']);
    }

    if (isset($_POST['despatchSale'])) {
        $SQL = $conn->prepare('UPDATE pedido SET CodigoFrete=?, Situacao=? WHERE IDPedido=?');
        $SQL->execute([$_POST['codigoFrete'], '3', $_GET['ID']]);

        header("location: salesInfo.php?ID=".$_GET['ID']);
    }

    if (isset($_POST['cancelSale'])) {
        $SQL = $conn->prepare('UPDATE pedido SET Situacao=? WHERE IDPedido=?');
        $SQL->execute(['4', $_GET['ID']]);

        header("location: salesInfo.php?ID=".$_GET['ID']);
    }

    if (isset($_POST['deliveredSale'])) {
        $SQL = $conn->prepare('UPDATE pedido SET Situacao=? WHERE IDPedido=?');
        $SQL->execute(['5', $_GET['ID']]);

        header("location: salesInfo.php?ID=".$_GET['ID']);
    }
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <title>Follow | Pedidos | Ecommerce</title>
        <?php include('PHP/meta.php') ?>
        <link rel="stylesheet" type="text/css" href="CSS/sales.css">
    </head>
    <body>
        <?php include('PHP/header.php') ?>
        <main>
            <h2 class='txtPages txtUpper txt400'><a href="dashboard.php" class='txtPages txtNoDecoration'>Painel</a> / 
                                                 <a href="sales.php" class='txtPages txtNoDecoration'>Pedidos</a> / Dados do Pedido</h2>
            <div class='divFlex' style='margin-top:15px;justify-content:center;'>
                <div id='info'>
                    <h2 class='txt500 txtCenter'>#<?php echo $codigo;?></h2>
                    <h1 class='txtGreen txt700 txtCenter'><?php echo $valorLiquido; ?></h1>
                    <h4 class='txt500 txtCenter'><?php echo $situacao; ?></h4>
                    <h4 class='txt500 txtCenter' style='position:absolute;right:0;top:33px;'><?php echo $dataCriacao; ?></h4>
                    <div id='actions'>
                        <?php echo $botoes; ?>
                    </div>
                    <div style='width:100%;height:1px;background-color:#E2E2E2;margin-bottom:27px;'></div>
                    <div style='width:max-content;margin:auto;display:flex;'>
                        <div style='margin-right:60px'>
                            <h3 class='txt700 txtCenter'><?php echo $item; ?></h3>
                            <h4 class='txtCenter txt500'>ITENS</h4>
                        </div>
                        <div>
                            <h3 class='txt700 txtCenter'><?php echo $valorFrete; ?></h3>
                            <h4 class='txtCenter txt500'>FRETE</h4>
                        </div>
                    </div>
                    <div id='formas'>
                        <div class='forma' style='margin-right:30px;'>
                            <div class='forma1'>
                                <h1 class='txtBlack txt700'>Forma de Entrega</h1>
                                <h2 class='txtBlack txt500'><?php echo $formaEntrega;?></h2>
                            </div>
                            <div class='forma2'>
                                <h1 class='txtBlack txt700'>Endereço de Entrega</h1>
                                <h2 class='txtBlack txt500'><b>Endereço:</b> <?php echo $enderecoEntrega;?></h2>
                                <h2 class='txtBlack txt500'><b>Bairro:</b> <?php echo $bairroEntrega;?></h2>
                                <h2 class='txtBlack txt500'><b>CEP:</b> <?php echo $cepEntrega;?></h2>
                                <h2 class='txtBlack txt500'><?php echo $municipioEntrega . ', ' . $ufEntrega . ', ' . $paisEntrega;?></h2>
                            </div>
                        </div>
                        <div class='forma'>
                            <div class='forma1'>
                                <h1 class='txtBlack txt700'>Forma de Pagamento</h1>
                                <h2 class='txtBlack txt500' style='margin-top:15px'><?php echo $formaCobranca;?></h2>
                            </div>
                            <div class='forma2'>
                                <h1 class='txtBlack txt700'>Endereço de Cobrança</h1>
                                <h2 class='txtBlack txt500'><b>Endereço:</b> <?php echo $enderecoCobranca;?></h2>
                                <h2 class='txtBlack txt500'><b>Bairro:</b> <?php echo $bairroCobranca;?></h2>
                                <h2 class='txtBlack txt500'><b>CEP:</b> <?php echo $cepCobranca;?></h2>
                                <h2 class='txtBlack txt500'><?php echo $municipioCobranca . ', ' . $ufCobranca . ', ' . $paisCobranca;?></h2>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <h1 class='txtTitle txt500 tbWhite' style='margin-top:20px;margin-bottom:20px'>Itens do Pedido</h1>
            <div class='divFlex'>
                <?php
                    $SQLItem = $conn->prepare('SELECT * FROM pedido_item WHERE IDPedido=?');
                    $SQLItem->execute([$_GET['ID']]);
                    $SQLItem = $SQLItem->fetchAll();
                    foreach ($SQLItem as $value) {
                        $SQLProduto = $conn->prepare('SELECT * FROM produtos WHERE IDProduto=? LIMIT 1');
                        $SQLProduto->execute([$value['IDProduto']]);
                        $SQLProduto = $SQLProduto->fetch();

                        echo '<div class="product" style="background-image: url('."'".IMAGE.$SQLProduto['Imagem']."'".')">';
                            echo "<div class='quantity'>";
                                echo "<h2 class='txtWhite txtCenter txt800'>".$value['Quantidade']."</h2>";
                            echo "</div>";
                            echo "<div class='size'>";
                                echo "<h2 class='txtWhite txtCenter txt700'>".$value['Tamanho']."</h2>";
                            echo "</div>";
                            echo "<div class='color' style='background-color: ".$value['Cor']."'></div>";
                            echo "<div class='price'>";
                                echo "<h2 class='txtBlack txtCenter txt800'>R$ ".$value['ValorLiquido']."</h2>";
                            echo "</div>";
                            echo "<div class='reference'>";
                                echo "<h2 class='txtWhite txtCenter txt600'>".$value['Codigo']."</h2>";
                            echo "</div>";
                            echo "<div class='name'>";
                                echo "<h1 class='txtUpper txtWhite txtCenter'>".$value['Nome']."</h1>";
                            echo "</div>";
                        echo "</div>";
                    }
                ?>
            </div>
            <div id='modalCancel' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleRed">
                        <h1 class='txtUpper txtWhite txt600'>Cancelamento de Pedido</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>Realmente deseja cancelar o pedido <b><?php echo $codigo;?></b>?</h2>
                        </div>
                        <form method='post'>
                            <input type="submit" name='cancelSale' class='button btRed btRound bt100 btModalSelect txtWhite txt600' value='CANCELAR PEDIDO'>
                        </form>
                    </div>
                </div>
            </div>
            <div id='modalRelease' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Liberação de Pedido</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>Realmente deseja liberar o pedido <b><?php echo $codigo;?></b>?</h2>
                        </div>
                        <form method='post'>
                            <input type="submit" name='releaseSale' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='LIBERAR PEDIDO'>
                        </form>
                    </div>
                </div>
            </div>
            <div id='modalDespatch' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Despachar Pedido</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>Deseja despachar o pedido <b><?php echo $codigo;?></b>?</h2>
                        </div>
                        <form method='post'>
                        <?php
                            if ($formaEntrega == 'Frete')
                                echo "<center><input type='text' name='codigoFrete' placeholder='Código do Frete' class='input ipRound ipBorder ip100 txtCenter txt600' style='font-size:20px;margin-top:-30px;margin-bottom:20px;height:30px'></center>";
                        ?>
                            <input type="submit" name='despatchSale' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='DESPACHAR PEDIDO'>
                        </form>
                    </div>
                </div>
            </div>
            <div id='modalDelivered' class='modalContainer closeModal'>
                <div class="modal">
                    <div class="mdTitle mdTitleBlue">
                        <h1 class='txtUpper txtWhite txt600'>Entrega de Pedido</h1>
                        <button class='button closeModal'>
                            <div class='closeModal x1'></div>
                            <div class='closeModal x2'></div>
                        </button>
                    </div>
                    <div class='mainModal'>
                        <div class='center'>
                            <h1 class='txtBlack txtCenter txt600'>Você tem certeza?</h1></br>
                            <h2 class='txtBlack txtCenter txt500'>O pedido <b><?php echo $codigo;?></b> foi entregue ao destinatário?</h2>
                        </div>
                        <form method='post'>
                            <input type="submit" name='deliveredSale' class='button btVar btRound bt100 btModalSelect txtWhite txt600' value='ENTREGAR PEDIDO'>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </body>
</html>