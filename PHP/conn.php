<?php
    ob_start();
    session_start();

    date_default_timezone_set('Brazil/east');

    ini_set("display_errors", 1);
    error_reporting(-1);

    try {
        if (!defined('HOST')) {
            // define('HOST','localhost');
            // define('PORT','');
            // define('USER','root');
            // define('PASS','Cupom@System123');

            define('HOST','localhost');
            define('PORT','41890');
            define('USER','u634078428_follow');
            define('PASS','Somos@Follow7');
            define('DATA','u634078428_ecommerce');
        }
        
        if (!defined('IMAGE') && isset($_SESSION['LOGIN'])) {
            // define('IMAGE','http://cupomautomacao.com/Ecommerce/IMG/Product/' . $_SESSION['LOGIN']['IDFilial'] . '/');
            define('IMAGE','IMG/Product/' . $_SESSION['LOGIN']['IDFilial'] . '/');
        }

        $conn = new PDO('mysql:host='.HOST.';port='.PORT.';dbname='.DATA,USER, PASS);
        // $conn = new PDO('mysql:host=sql436.main-hosting.eu;port=41890;dbname=u634078428_ecommerce','u634078428_follow','Somos@Follow7');

    } catch (PDOException $e) {
        echo $e;
        die();
    }
?>