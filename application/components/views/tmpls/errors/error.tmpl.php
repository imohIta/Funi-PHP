<?php
global $registry;

if(!defined('PATH')){
    define ("PATH", realpath(__DIR__ . '/../'));

    $parts = explode("/", PATH);
    define("PARENT_DIR", $parts[count($parts) - 1]);
}

if($registry){
    $config = $registry->get('config');

    if($config){
        $baseUri =  $config->get('baseUri');
        $appTitle = $registry->get('config')->get('appTitle');
    }
}

$baseUri = $baseUri ?? 'http://' . $_SERVER['HTTP_HOST'] . '/' . PARENT_DIR . '/public';
$appTitle = $appTitle ?? PARENT_DIR;

?>
<!DOCTYPE html>
<html lang="en">


<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

	<title><?php echo $appTitle; ?> </title>

	<meta name="description" content="">
	<meta name="author" content="Imoh Ita">

    <link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/setUp/css/bootstrap.css">
 	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/setUp/css/font.css">
 	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/setUp/css/font-awesome.css">
 	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/setUp/css/custom.css">
 	<link rel="stylesheet" href="<?php echo $baseUri; ?>/assets/setUp/css/styles.css">

    <!--[if lt IE 9]>
 		<script src="../js/html5shiv.min.js"></script>
 		<script src="../js/respond.min.js"></script>
 	<![endif]-->

</head>
<body>


    <div class="container" style="text-align:center">

        <!-- <div class="box-big "> -->
            <div class="panel col-md-12" style="margin-top:10px; min-height:690px">
    			<div class="panel-body">
                    <div class="logo-holder" style="margin-top:20%">
                        <p><span class="logo-txt">Error</span></p>
                        <p class="logo-txt-sm">( <?php echo $code; ?> )</p>

                        <br /><hr/>

                        <p style="margin-top:10px; font-size:22px; color:#efb8b8;">
                            <?php echo $msg; ?>
                        </p>

                        <p style="margin-top:10px; font-size:18px;">
                            <a class="btn btn-danger" href="<?php echo $baseUri; ?>/home">Back to Dashboard</a>
                        </p>

                    </div>
                </div>
            </div>
        <!-- </div> -->


    </div>



</body>

</html>
