<?php

error_reporting(1);
ini_set('display_errors', '1');

    session_start();


    // if(!isset($_SESSION['proceedTo2'])){
    //     header('Location: index');
	// 	exit;
    // }

    unset($_SESSION['proceedTo2']);

    /**
     * Encrypt and decrypt
     *
     * @author Nazmul Ahsan <n.mukto@gmail.com>
     * @link http://nazmulahsan.me/simple-two-way-function-encrypt-decrypt-string/
     *
     * @param string $string string to be encrypted/decrypted
     * @param string $action what to do with this? e for encrypt, d for decrypt
     */
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

    function alertBox($msg, $icon = "", $type = "") {
        return "
        <div class=\"alertMsg $type\">
          <div class=\"msgIcon pull-left\">$icon</div>
          $msg
          <a class=\"msgClose\" title=\"Close\" href=\"#\"><i class=\"fa fa-times\"></i></a>
        </div>
      ";
    }

    if(isset($_POST['submit'])){

        include ('../core/configs/dbConfig.php');

        # connect
        $dsn = $dsn = "mysql:host=$_SESSION[dbHost];charset=utf8";
        $pdo = new PDO($dsn, $_SESSION['dbUser'], $_SESSION['dbPwd']);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $pdo->query("USE `$_SESSION[dbName]`");

        // Settings Validations
		if($_POST['baseUrl'] == "") {
			$msgBox = alertBox("Please enter the Installation URL (include the trailing slash).", "<i class='fa fa-times-circle'></i>", "danger");
		} else if($_POST['appName'] == "") {
			$msgBox = alertBox("Please enter an Application Name.", "<i class='fa fa-times-circle'></i>", "danger");
		}else{

            $baseUrl = filter_var($_POST['baseUrl'], FILTER_SANITIZE_STRING);

            if(filter_var($baseUrl, FILTER_VALIDATE_URL) === false){

                $msgBox = alertBox("Please enter a Valid Url.", "<i class='fa fa-times-circle'></i>", "danger");

            }else{

                $appName = filter_var($_POST['appName'], FILTER_SANITIZE_STRING);

                # remove all white spaces from appName
                $appName = preg_replace('/\s+/', '', $appName);

                # create a file and save the app name
                file_put_contents('../core/configs/appName.txt', $appName);

                # store app Name in Session
                $_SESSION['appName'] = $appName;

                $devSettings = json_encode(array(
                    'appName' => $appName,
                    'baseUrl' => $baseUrl,
                    'dbName' => $_SESSION['dbName'],
                    'dbHost' => $_SESSION['dbHost'],
                    'dbUser' => $_SESSION['dbUser'],
                    'dbPwd' => simpleCrypt($_SESSION['dbPwd']),
                    'timezone' => $_SESSION['timezone']
                ));

                $prodSettings = json_encode(array(
                    'appName' => '',
                    'baseUrl' => '',
                    'dbName' => '',
                    'dbHost' => '',
                    'dbUser' => '',
                    'dbPwd' => ''
                ));

                try {

                    $st = $pdo->prepare("insert into funiSettings ( id, development, production ) values ( 1, :development, :production)");
                    $st->bindParam(':development', $devSettings, PDO::PARAM_STR);
                    $st->bindParam(':production', $prodSettings, PDO::PARAM_STR);


                    $st->execute();
                    $st = null;

                    $_SESSION['finish'] = true;

                    header('Location: complete');
                    exit;

                } catch (PDOException $e) {
                    die('Error Occured ' . var_dump($e->errorInfo));
                }

            }

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
   <title>Funi &middot; Installation &amp; Set Up</title>

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

                       <p style="margin-top:10px; font-size:20px; color:#f4d53a;">
                           ( APPLICATION SET UP )
                       </p>

                   </div>
               <!-- </div>
           </div> -->
       </div>

       <div class="box-big pull-left">

           <div class="alertMsg success">
    			<div class="msgIcon pull-left">
    				<i class="fa fa-check"></i>
    			</div>
    			Your database has been correctly configured.
    		</div>

           <div class="panel panel-warning mt-10">
                   <div class="panel-heading">Step 2 : Global Settings</div>
                   <div class="panel-body">

                       <p class="text-warning"><strong>Now take a few minutes and complete the information below in order to finish installing Funi<sup>1.0</sup>.</strong></p>

                       <form action="step2" method="post">

                           <div class="row">
                               <div class="col-md-12">
                                   <div class="form-group">
                                       <label for="siteName">Application Name</label>
                                       <input type="text" class="form-control" id="appName" name="appName" required="required" value="<?php echo isset($_POST['appName']) ? $_POST['appName'] : ''; ?>" onkeyup="trimWhiteSpace('appName', this.value); buildBaseUrl(this.value);"/>
                                       <span class="help-block">ie. The name of your application. Funi1.0 will be renamed to this after installation ( Camel Case preferablly )</span>
                                   </div>
                               </div>
                           </div>

							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="installUrl">Base URL</label>
										<input type="text" class="form-control" name="baseUrl" id="baseUrl" required="required" value="<?php echo isset($_POST['baseUrl']) ? $_POST['baseUrl'] : ''; ?>" placeholder="http://localhost/appName/public" readonly />
										<span class="help-block">i.e BaseUrl of your application. This is the full url of your application in your local computer </span>
									</div>
								</div>
                            </div>

                            <br style="margin-top:40px" />

							<span class="pull-right">

                                <input type="hidden" id="serverHost" value="<?php echo $_SERVER['HTTP_HOST']; ?>" />

								<button name="submit" type="submit" class="btn btn-success btn-icon mt-40"><i class="fa fa-check"></i> Complete Install</button>
							</span>
						</form>

                        <br />


                   </div>
               </div>

       </div>


   </div>

   <script type="text/javascript" src="../public/assets/js/funiCtrl.js"></script>
   <script>

        function trimWhiteSpace(div, value){
            setValue(div, value.replace(' ', ''));
        }

        function buildBaseUrl(value){

            if(value != ''){
                value = value.replace(' ', '');
                var serverHost = getValue('serverHost');
                var url = 'http://' + serverHost + '/' + value + '/public';
                setValue('baseUrl', url);

            }else{
                setValue('baseUrl', '');
            }
        }

   </script>

</body>
</html>
