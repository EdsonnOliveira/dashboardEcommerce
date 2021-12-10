<header id='h1'>
    <nav>
        <ul>
            <li>
                <a href="dashboard.php">
                    
                </a>
            </li>
            <li>
                <a href="dashboard.php">
                    <img src="IMG/Custom/home.png" alt="Icon Home"></br>
                    PAINEL
                </a>
            </li>
            <li>
                <a href="product.php">
                    <img src="IMG/Custom/product.png" alt="Icon Product"></br>
                    PRODUTOS
                </a>
            </li>
            <li>
                <a href="sales.php">
                    <img src="IMG/Custom/cart.png" alt="Icon Cart"></br>
                    PEDIDOS
                </a>
            </li>
            <li>
                <a href="newsletter.php" style='font-size:16px'>
                    <img src="IMG/Custom/email.png" alt="Icon Report"></br>
                    NEWSLETTER
                </a>
            </li>
            <li>
                <a href="report.php" style='font-size:16px'>
                    <img src="IMG/Custom/report.png" alt="Icon Report"></br>
                    RELATÓRIOS
                </a>
            </li>
        </ul>
    </nav>
</header>
<header id='h2'>
    <nav style='margin-left: 22px;'>
        <ul>
            <li><a href="">AJUDA</a></li>
        </ul>
    </nav>
    <nav style='margin-right: 22px;'>
        <ul style='display:flex' class='menuUser'>
            <img src="IMG/Custom/user.png" alt="Icon User" class='menuUser'><li class='txtUpper menuUser' style='margin-top:3px'><?php echo $_SESSION['LOGIN']['Nome']?></li>
        </ul>
        <div id='showMenu'>
            <ul>
                <li><a href="index.php?close">SAIR</a></li>
            </ul>
        </div>
    </nav>
</header>
<script src='JS/header.js'></script>