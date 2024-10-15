<?php
    require_once "stripe-php-master/init.php";

    $stripeDetails = array(
        "secretKey" => "sk_test_51Q4YtKB2GV3voZGNGQwrxSDOi7ZgaT421xpA0MEf8rjofcADlZMzM9krtHjNhx6w7gN77200dS4aJB4cT5odVxud00V23BRdbX",
        "publishableKey" => "pk_test_51Q4YtKB2GV3voZGNaqTVoGF16ihTeLaqHh0K1Eg3msOiocuRBLb2fOEgbL79aD9Oxh2agXOIWCUSZSSlyl4Rew8N00DGwnoLx7"
    );
    
    \Stripe\Stripe::setApiKey($stripeDetails["secretKey"]);
?>