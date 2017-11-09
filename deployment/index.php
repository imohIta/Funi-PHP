<?php


    define ("PATH", realpath(__DIR__ . '/../'));

    $parts = explode("/", PATH);
    define("PARENT_DIR", $parts[count($parts) - 1]);


    session_start();


    function alertBox($msg, $icon = "", $type = "") {
        return "
				<div class=\"alertMsg $type\">
					<div class=\"msgIcon pull-left\">$icon</div>
					$msg
					<a class=\"msgClose\" title=\"Close\" href=\"#\"><i class=\"fa fa-times\"></i></a>
				</div>
			";
    }

    function simpleCrypt( $string, $action = 'e' ) {

        // you may change these values to your own
        $secret_key = 'hApPYwiFEHaPpyL1f3';
        $secret_iv = 'SYz@A+min0_0';

        $output = false;
        $encrypt_method = "AES-256-CBC";
        $key = hash( 'sha256', $secret_key );
        $iv = substr( hash( 'sha256', $secret_iv ), 0, 16 );

        if( $action == 'e' ) {
            $output = base64_encode( openssl_encrypt( $string, $encrypt_method, $key, 0, $iv ) );
        }
        else if( $action == 'd' ){
            $output = openssl_decrypt( base64_decode( $string ), $encrypt_method, $key, 0, $iv );
        }

        return $output;
    }

    # initialize params
    $dbHost = $dbUser =  $dbPwd = $dbName = $baseUrl = $appName = '';

    # check if config file was created
    if (is_file(PATH . '/core/configs/dbConfig.php')) {

        require_once PATH . '/core/configs/dbConfig.php';



    }else{

        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . PARENT_DIR . '/install/');
        exit;

    }



    # check if funiDeploymentSettings have already been set
    $pdo->query("USE `$dbName`");
    $st = $pdo->prepare('SELECT * FROM `funiSettings` where `id` = 1');
    $st->execute();
    $result = $st->fetch(PDO::FETCH_ASSOC);



    if($result !== false && !is_null($result)){

        $settings = json_decode($result['production']);

        $dbHost = $settings->dbHost;
        $dbName = $settings->dbName;
        $dbPwd = simpleCrypt($settings->dbPwd, 'd');
        $dbUser = $settings->dbUser;
        $appName = $settings->appName;
        $baseUrl = $settings->baseUrl;

    }



    if(isset($_POST['submit'])){

        $dbHost = filter_var($_POST['dbhost'], FILTER_SANITIZE_STRING);
		$dbUser = filter_var($_POST['dbuser'], FILTER_SANITIZE_STRING);
		$dbPwd = filter_var($_POST['dbpass'], FILTER_SANITIZE_STRING);
		$dbName = filter_var($_POST['dbname'], FILTER_SANITIZE_STRING);

        $baseUrl = filter_var($_POST['baseUrl'], FILTER_SANITIZE_STRING);

        if(filter_var($baseUrl, FILTER_VALIDATE_URL) === false){
            //$msgBox = alertBox("Please enter a Valid Url.", "<i class='fa fa-times-circle'></i>", "danger");
            $errorMsg = "Please enter a Valid Url";

        }else{

            $appName = filter_var($_POST['appName'], FILTER_SANITIZE_STRING);

            $settings = json_encode(array(
                'appName' => $appName,
                'baseUrl' => $baseUrl,
                'dbName' => $_SESSION['dbName'],
                'dbHost' => $_SESSION['dbHost'],
                'dbUser' => $_SESSION['dbUser'],
                'dbPwd' => simpleCrypt($_SESSION['dbPwd'])
            ));


            # update settings
            $st = $pdo->prepare('update funiSettings set production = :production where id = 1');
            $st->bindValue(':production', $settings, PDO::PARAM_STR);
            $st->execute();
            $st = null;

            $successMsg = "Deployment Paramters successfully set";


        }



    }

 ?>

 <!DOCTYPE html>
 <html lang="en">
 <head>
 	<meta charset="utf-8">
 	<meta http-equiv="X-UA-Compatible" content="IE=edge">
 	<meta name="viewport" content="width=device-width, initial-scale=1">
 	<meta name="description" content="">
 	<meta name="author" content="">
 	<title>Funi &middot; App Deployment</title>

 	<link rel="stylesheet" href="../public/assets/setUp/css/bootstrap.css">
 	<link rel="stylesheet" href="../public/assets/setUp/css/font.css">
 	<link rel="stylesheet" href="../public/assets/setUp/css/font-awesome.css">
 	<link rel="stylesheet" href="../public/assets/setUp/css/custom.css">
 	<link rel="stylesheet" href="../public/assets/setUp/css/styles.css">

 	<!--[if lt IE 9]>
 		<script src="../js/html5shiv.min.js"></script>
 		<script src="../js/respond.min.js"></script>
 	<![endif]-->


 </head>

 <body>
	<div class="container">
		<!-- <div class="row">
			<div class="col-md-4 col-md-offset-4">
				<div class="signin-logo mt-20 mb-20 text-center">
					<a href="index.php"><img src="../images/email_logo.png" /></a>
				</div>
			</div>
		</div> -->

        <div class="box-small pull-left">
            <!-- <div class="panel" style="margin-top:10px">
    			<div class="panel-body"> -->
                    <div class="logo-holder">
                        <p><span class="logo-txt">Funi</span><sup class="logo-txt-sub">1.0<sup></p>
                        <p class="logo-txt-sm">PHP FRAMEWORK</p>

                        <p style="margin-top:10px; font-size:20px; color:#efb8b8;">
                            ( DEPLOYMENT SET UP )
                        </p>

                    </div>
                <!-- </div>
            </div> -->
        </div>

        <div class="box-big pull-left">

            <p class="lead text-center">So you are ready to go live...Let's make that hassle free</p>
            <div class="panel panel-danger mt-10">
    				<div class="panel-heading">Step 1 : Configure Live Database and Enter App Settings</div>
    				<div class="panel-body">

                        <?php
                            if(isset($errorMsg)){
                                echo alertBox($errorMsg, "<i class='fa fa-times-circle'></i>", "danger");
                            }elseif(isset($successMsg)){
                                echo alertBox($successMsg, "<i class='fa fa-times-circle'></i>", "success");
                            }
                        ?>

    					<p class="text-warning"><strong>Please enter information for your live Database &amp; Provide your Application Settings</strong></p>

                        <form action="" method="post" class="mt-10">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbhost">Host Name</label>
										<input type="text" class="form-control" name="dbhost" required="required" value="<?php echo $dbHost; ?>" />
										<span class="help-block">Hostname of your Live Database. Check with your Host Provider.</span>
									</div>
								</div>
                            </div>
                            <div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbname">Database Name</label>
										<input type="text" class="form-control" name="dbname" required="required" value="<?php echo $dbName; ?>" />
										<span class="help-block">The Database Name you want to use for your Live Database.</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbuser">Database Username</label>
										<input type="text" class="form-control" name="dbuser" required="required" value="<?php echo $dbUser; ?>" />
										<span class="help-block">The User allowed to connect to the Database.</span>
									</div>
								</div>
                            </div>
                            <div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbpass">Database User Password</label>
										<input type="password" class="form-control" name="dbpass" value="<?php echo $dbPwd; ?>" />
										<span class="help-block">The Password for the User allowed to connect to the Database.</span>
									</div>
								</div>
							</div>

                            <div class="row">
								<div class="col-md-6">
									<div class="form-group">
										<label for="firstName">Application Name</label>
										<input type="text" class="form-control" name="appName" required="required" value="<?php echo $appName; ?>" />
                                        <span class="help-block">ie. The name of your application. This will Appear at the title Bar</span>
									</div>
								</div>
								<div class="col-md-6">
									<div class="form-group">
										<label for="lastName">Base URL</label>
										<input type="text" class="form-control" name="baseUrl" required="required" value="<?php echo $baseUrl; ?>" />
                                        <span class="help-block">i.e BaseUrl of your application. This is the full url of your application online </span>
									</div>
								</div>
							</div>

							<span class="pull-right">
								<button type="input" name="submit" class="btn btn-success btn-icon mt-10"><i class="fa fa-check"></i> Finish</button>
							</span>
						</form>




    				</div>
    			</div>

        </div>


    </div>
</body>
</html>
