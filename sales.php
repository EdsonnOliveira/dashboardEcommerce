<?php
    include('PHP/conn.php');

    if (!isset($_SESSION['LOGIN']))
        header("location:index.php?close");
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
            <h2 class='txtWhite txtPages txtUpper txt400'><a href="dashboard.php" class='txtWhite txtNoDecoration'>Painel</a> / Pedidos</h2>
            <h1 class='txtWhite txtTitle txt500'>Pedidos</h1>
            <div class='divFlex' style='margin-top:15px'>
                <table class='table tbRound tbWhite tbShadow tb100'>
                    <tr>
                        <th style='border-bottom: 1px solid #CECECE;'>#</th>
                        <th class='txtLeft' style='border-bottom: 1px solid #CECECE;'>CLIENTE</th>
                        <th style='border-bottom: 1px solid #CECECE;'>DATA</th>
                        <th style='border-bottom: 1px solid #CECECE;'>DATA ENTREGA</th>
                        <th style='border-bottom: 1px solid #CECECE;'>VALOR</th>
                        <th style='border-bottom: 1px solid #CECECE;'>STATUS</th>
                    </tr>
                    <?php
                        $SQL = $conn->prepare('SELECT * FROM pedido WHERE Situacao<>0 AND IDFilial=?');
                        $SQL->execute([$_SESSION['LOGIN']['IDFilial']]);
                        $SQL = $SQL->fetchAll();
                        $i = 1;
                        foreach ($SQL as $value) {
                            $ID = $value['IDPedido'];
                            if(($i % 2) == 1){
                                $color = "";
                            }else{
                                $color = "trCian";
                            }

                            echo "<tr class='".$color."'>";
                                echo "<td class='txtCenter txt700'><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtBlack'>".$value['IDPedido']."</a></td>";
                                echo "<td class='txt500'><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtBlack'>".$value['NomeCliente']."</a></td>";
                                echo "<td class='txtCenter txt500'><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtBlack'>".$value['DataCriacao']. ' '.$value['HoraCriacao']."</a></td>";
                                echo "<td class='txtCenter txt500'><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtBlack'>".$value['DataEntrega']. ' '.$value['HoraEntrega']."</a></td>";
                                echo "<td class='txtCenter txt700'><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtGreen'> R$ ".$value['ValorLiquido']."</a></td>";
                                        switch ($value['Situacao']) {
                                            case 1:
                                                echo "<td class='tdStatus tdBlue'>";
                                                    echo "<div>";
                                                        echo "<h5><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtWhite'>AGUARDANDO</a></h5>";
                                                    echo "</div>";
                                                echo "</td>";
                                                break;
                                            case 2:
                                                echo "<td class='tdStatus tdYellow'>";
                                                    echo "<div>";
                                                        echo "<h5><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtBlack'>PENDENTE</a></h5>";
                                                    echo "</div>";
                                                echo "</td>";
                                                break;
                                            case 3:
                                                echo "<td class='tdStatus tdBlue'>";
                                                    echo "<div>";
                                                        echo "<h5><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtWhite'>ENVIADO</a></h5>";
                                                    echo "</div>";
                                                echo "</td>";
                                                break;
                                            case 4:
                                                echo "<td class='tdStatus tdRed'>";
                                                    echo "<div>";
                                                        echo "<h5><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtWhite'>CANCELADA</a></h5>";
                                                    echo "</div>";
                                                echo "</td>";
                                                break;
                                            case 5:
                                                echo "<td class='tdStatus tdGreen'>";
                                                    echo "<div>";
                                                        echo "<h5><a href='salesInfo.php?ID=$ID' class='txtNoDecoration txtWhite'>ENTREGUE</a></h5>";
                                                    echo "</div>";
                                                echo "</td>";
                                                break;
                                        }
                            echo "</tr>";
                            $i++;
                        }
                    ?>
                </table>
                <div class='filter'>
                    <form>
                        <input type="text" name='search' class='input ip100 ipRound txtBlack txt600' placeholder='PROCURAR...' style='height: 15px; margin-bottom: 10px;'>
                        <input type="submit" class='button btVar btMedium bt100 btRound txtWhite txt700' value='FILTRAR'>
                    </form>
                </div>
            </div>
        </main>
    </body>
</html>