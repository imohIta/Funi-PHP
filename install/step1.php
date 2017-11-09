<?php

    session_start();


    if(!isset($_POST['submit'])){
        header('Location: index');
		exit;
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

    if($_POST['submit'] == 'Step 2'){

        $msgBox = '';

        // Validation
        if($_POST['dbhost'] == '') {
			$msgBox = alertBox("Please enter in your Host name. This is usually 'localhost'.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dbuser'] == '') {
			$msgBox = alertBox("Please enter the username for the database.", "<i class='fa fa-times-circle'></i>", "danger");
        } else if($_POST['dbname'] == '') {
			$msgBox = alertBox("Please enter the database name.", "<i class='fa fa-times-circle'></i>", "danger");
        } else {
			$dbHost = filter_var($_POST['dbhost'], FILTER_SANITIZE_STRING);
			$dbUser = filter_var($_POST['dbuser'], FILTER_SANITIZE_STRING);
			$dbPwd = filter_var($_POST['dbpass'], FILTER_SANITIZE_STRING);
			$dbName = filter_var($_POST['dbname'], FILTER_SANITIZE_STRING);
			$timezone = filter_var($_POST['timezone'], FILTER_SANITIZE_STRING);

            $_SESSION['dbHost'] = $dbHost;
            $_SESSION['dbUser'] = $dbUser;
            $_SESSION['dbPwd'] = $dbPwd;
            $_SESSION['dbName'] = $dbName;
            $_SESSION['timezone'] = $timezone;

            # build db connection strings and append connection codes in config.txt
            $str ="<?php

                	date_default_timezone_set('".$timezone."');

                	$"."dbHost = '".$dbHost."';
                	$"."dbUser = '".$dbUser."';
                	$"."dbPwd = '".$dbPwd."';
                	$"."dbName = '".$dbName."';

                	".file_get_contents('config.txt')."
                ?>";
            # try to write genrated string into a new file config.php
            if (!file_put_contents('../core/configs/dbConfig.php', $str)) {
                $no_perm = true;
            }


            # if new config file was created
            if (is_file('../core/configs/dbConfig.php')) {
        		//include ('../config.php');

        		// Errors on for the Install
        		error_reporting(E_ALL);
        		ini_set('display_errors', '1');

                try {

                    # connect
                    $dsn = $dsn = "mysql:host=$dbHost;charset=utf8";
                    $pdo = new PDO($dsn, $dbUser, $dbPwd);

                    # create new Database
                    $pdo->query("CREATE DATABASE IF NOT EXISTS `$dbName` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci");

                    # use db
                    $pdo->query("USE `$dbName`");

                    # read install sql
                    $sql = file_get_contents('install.sql');
                    if (!$sql){
                        die ('Error opening Installation SQL file');
                    }

                    try {

                        $pdo->exec($sql);

                        $_SESSION['proceedTo2'] = true;


                        # redirect to step 2
                        header('Location: step2');
                		exit;

                    } catch (PDOException $e) {

                        die('Error Occured ' . $e->getMessage());
                    }


                } catch (PDOException $e) {

                    # set error message

                    # redirect to step 1
                    header('Location: step1');
                    exit;
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

            <p class="lead text-center">Setting up Funi is as easy as ABC..just 2 simple steps & we are good</p>
            <div class="panel panel-warning mt-10">
    				<div class="panel-heading">Step 1 : Configure Database and Select Time Zone</div>
    				<div class="panel-body">

                        <?php if (isset($no_perm)) { ?>
    						<p class="lead">
    							You dont have permissions to create a new file. Please CHMOD the root folder to 755 and then <a href="index">refresh this page</a>.<br />
    							Not sure how? There are many <a href="https://www.google.com/search?q=how+to+chmod+with+php&ie=utf-8&oe=utf-8" target="_blank">tutorials on the web</a> that explain this in detail.
    						</p>
    						<a href="index" class="btn btn-primary">Refresh Page</a>
					    <?php } ?>

    					<p class="text-warning"><strong>Please type in your database information &amp; select a Time Zone.</strong></p>

                        <form action="" method="post" class="mt-10">
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbhost">Host Name</label>
										<input type="text" class="form-control" name="dbhost" required="required" value="localhost" />
										<span class="help-block">Usually 'localhost'. Check with your Host Provider.</span>
									</div>
								</div>
                            </div>
                            <div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbname">Database Name</label>
										<input type="text" class="form-control" name="dbname" required="required" value="<?php echo isset($_POST['dbname']) ? $_POST['dbname'] : '' ?>" />
										<span class="help-block">The Database Name you want to use for your Application.</span>
									</div>
								</div>
							</div>
							<div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbuser">Database Username</label>
										<input type="text" class="form-control" name="dbuser" required="required" value="<?php echo isset($_POST['dbuser']) ? $_POST['dbuser'] : '' ?>" />
										<span class="help-block">The User allowed to connect to the Database.</span>
									</div>
								</div>
                            </div>
                            <div class="row">
								<div class="col-md-12">
									<div class="form-group">
										<label for="dbpass">Database User Password</label>
										<input type="password" class="form-control" name="dbpass" value="<?php echo isset($_POST['dbpass']) ? $_POST['dbpass'] : '' ?>" />
										<span class="help-block">The Password for the User allowed to connect to the Database.</span>
									</div>
								</div>
							</div>

                            <div class="form-group">
								<label for="timezone">Select Time Zone</label>
								<select class="form-control" name="timezone">
									<option value="Pacific/Midway">(GMT-11:00) Midway Island, Samoa</option>
									<option value="America/Adak">(GMT-10:00) Hawaii-Aleutian</option>
									<option value="Etc/GMT+10">(GMT-10:00) Hawaii</option>
									<option value="Pacific/Marquesas">(GMT-09:30) Marquesas Islands</option>
									<option value="Pacific/Gambier">(GMT-09:00) Gambier Islands</option>
									<option value="America/Anchorage">(GMT-09:00) Alaska</option>
									<option value="America/Ensenada">(GMT-08:00) Tijuana, Baja California</option>
									<option value="Etc/GMT+8">(GMT-08:00) Pitcairn Islands</option>
									<option value="America/Los_Angeles">(GMT-08:00) Pacific Time (US & Canada)</option>
									<option value="America/Denver">(GMT-07:00) Mountain Time (US & Canada)</option>
									<option value="America/Chihuahua">(GMT-07:00) Chihuahua, La Paz, Mazatlan</option>
									<option value="America/Dawson_Creek">(GMT-07:00) Arizona</option>
									<option value="America/Belize">(GMT-06:00) Saskatchewan, Central America</option>
									<option value="America/Cancun">(GMT-06:00) Guadalajara, Mexico City, Monterrey</option>
									<option value="Chile/EasterIsland">(GMT-06:00) Easter Island</option>
									<option value="America/Chicago">(GMT-06:00) Central Time (US & Canada)</option>
									<option value="America/New_York" selected>(GMT-05:00) Eastern Time (US & Canada)</option>
									<option value="America/Havana">(GMT-05:00) Cuba</option>
									<option value="America/Bogota">(GMT-05:00) Bogota, Lima, Quito, Rio Branco</option>
									<option value="America/Caracas">(GMT-04:30) Caracas</option>
									<option value="America/Santiago">(GMT-04:00) Santiago</option>
									<option value="America/La_Paz">(GMT-04:00) La Paz</option>
									<option value="Atlantic/Stanley">(GMT-04:00) Faukland Islands</option>
									<option value="America/Campo_Grande">(GMT-04:00) Brazil</option>
									<option value="America/Goose_Bay">(GMT-04:00) Atlantic Time (Goose Bay)</option>
									<option value="America/Glace_Bay">(GMT-04:00) Atlantic Time (Canada)</option>
									<option value="America/St_Johns">(GMT-03:30) Newfoundland</option>
									<option value="America/Araguaina">(GMT-03:00) UTC-3</option>
									<option value="America/Montevideo">(GMT-03:00) Montevideo</option>
									<option value="America/Miquelon">(GMT-03:00) Miquelon, St. Pierre</option>
									<option value="America/Godthab">(GMT-03:00) Greenland</option>
									<option value="America/Argentina/Buenos_Aires">(GMT-03:00) Buenos Aires</option>
									<option value="America/Sao_Paulo">(GMT-03:00) Brasilia</option>
									<option value="America/Noronha">(GMT-02:00) Mid-Atlantic</option>
									<option value="Atlantic/Cape_Verde">(GMT-01:00) Cape Verde Is.</option>
									<option value="Atlantic/Azores">(GMT-01:00) Azores</option>
									<option value="Europe/Belfast">(GMT) Greenwich Mean Time : Belfast</option>
									<option value="Europe/Dublin">(GMT) Greenwich Mean Time : Dublin</option>
									<option value="Europe/Lisbon">(GMT) Greenwich Mean Time : Lisbon</option>
									<option value="Europe/London">(GMT) Greenwich Mean Time : London</option>
									<option value="Africa/Abidjan">(GMT) Monrovia, Reykjavik</option>
									<option value="Europe/Amsterdam">(GMT+01:00) Amsterdam, Berlin, Bern, Rome, Stockholm, Vienna</option>
									<option value="Europe/Belgrade">(GMT+01:00) Belgrade, Bratislava, Budapest, Ljubljana, Prague</option>
									<option value="Europe/Brussels">(GMT+01:00) Brussels, Copenhagen, Madrid, Paris</option>
									<option value="Africa/Algiers">(GMT+01:00) West Central Africa</option>
									<option value="Africa/Windhoek">(GMT+01:00) Windhoek</option>
									<option value="Asia/Beirut">(GMT+02:00) Beirut</option>
									<option value="Africa/Cairo">(GMT+02:00) Cairo</option>
									<option value="Asia/Gaza">(GMT+02:00) Gaza</option>
									<option value="Africa/Blantyre">(GMT+02:00) Harare, Pretoria</option>
									<option value="Asia/Jerusalem">(GMT+02:00) Jerusalem</option>
									<option value="Europe/Minsk">(GMT+02:00) Minsk</option>
									<option value="Asia/Damascus">(GMT+02:00) Syria</option>
									<option value="Europe/Moscow">(GMT+03:00) Moscow, St. Petersburg, Volgograd</option>
									<option value="Africa/Addis_Ababa">(GMT+03:00) Nairobi</option>
									<option value="Asia/Tehran">(GMT+03:30) Tehran</option>
									<option value="Asia/Dubai">(GMT+04:00) Abu Dhabi, Muscat</option>
									<option value="Asia/Yerevan">(GMT+04:00) Yerevan</option>
									<option value="Asia/Kabul">(GMT+04:30) Kabul</option>
									<option value="Asia/Yekaterinburg">(GMT+05:00) Ekaterinburg</option>
									<option value="Asia/Tashkent">(GMT+05:00) Tashkent</option>
									<option value="Asia/Kolkata">(GMT+05:30) Chennai, Kolkata, Mumbai, New Delhi</option>
									<option value="Asia/Katmandu">(GMT+05:45) Kathmandu</option>
									<option value="Asia/Dhaka">(GMT+06:00) Astana, Dhaka</option>
									<option value="Asia/Novosibirsk">(GMT+06:00) Novosibirsk</option>
									<option value="Asia/Rangoon">(GMT+06:30) Yangon (Rangoon)</option>
									<option value="Asia/Bangkok">(GMT+07:00) Bangkok, Hanoi, Jakarta</option>
									<option value="Asia/Krasnoyarsk">(GMT+07:00) Krasnoyarsk</option>
									<option value="Asia/Hong_Kong">(GMT+08:00) Beijing, Chongqing, Hong Kong, Urumqi</option>
									<option value="Asia/Irkutsk">(GMT+08:00) Irkutsk, Ulaan Bataar</option>
									<option value="Australia/Perth">(GMT+08:00) Perth</option>
									<option value="Australia/Eucla">(GMT+08:45) Eucla</option>
									<option value="Asia/Tokyo">(GMT+09:00) Osaka, Sapporo, Tokyo</option>
									<option value="Asia/Seoul">(GMT+09:00) Seoul</option>
									<option value="Asia/Yakutsk">(GMT+09:00) Yakutsk</option>
									<option value="Australia/Adelaide">(GMT+09:30) Adelaide</option>
									<option value="Australia/Darwin">(GMT+09:30) Darwin</option>
									<option value="Australia/Brisbane">(GMT+10:00) Brisbane</option>
									<option value="Australia/Hobart">(GMT+10:00) Hobart</option>
									<option value="Asia/Vladivostok">(GMT+10:00) Vladivostok</option>
									<option value="Australia/Lord_Howe">(GMT+10:30) Lord Howe Island</option>
									<option value="Etc/GMT-11">(GMT+11:00) Solomon Is., New Caledonia</option>
									<option value="Asia/Magadan">(GMT+11:00) Magadan</option>
									<option value="Pacific/Norfolk">(GMT+11:30) Norfolk Island</option>
									<option value="Asia/Anadyr">(GMT+12:00) Anadyr, Kamchatka</option>
									<option value="Pacific/Auckland">(GMT+12:00) Auckland, Wellington</option>
									<option value="Etc/GMT-12">(GMT+12:00) Fiji, Kamchatka, Marshall Is.</option>
									<option value="Pacific/Chatham">(GMT+12:45) Chatham Islands</option>
									<option value="Pacific/Tongatapu">(GMT+13:00) Nuku'alofa</option>
									<option value="Pacific/Kiritimati">(GMT+14:00) Kiritimati</option>
								</select>
							</div>
							<span class="pull-right">
								<button type="input" name="submit" value="Step 2" class="btn btn-success btn-icon mt-10"><i class="fa fa-check"></i> Go to Step 2</button>
							</span>
						</form>




    				</div>
    			</div>

        </div>


    </div>
</body>
</html>
