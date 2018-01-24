<?php

    /**
    * Pre-Installation Check
    *
    */

    $canInstall = 'true';
    $svrErr	= 'false';
    $folderErr = 'false';

    # Check for PHP Version
	if (version_compare(PHP_VERSION, '7.0', '>=')) {
		$phpversion = PHP_VERSION;
		$phpcheck = '<i class="fa fa-check text-success"></i> PASS';
	} else {
		$phpversion = 'You need to have PHP Version 7.0 or higher Installed to run Funi1.0.';
		$phpcheck = '<i class="fa fa-times text-danger"></i> FAIL';
		$canInstall = 'false';
		$svrErr	= 'true';
	}

    # check PDO support
	if (class_exists('PDO')) {
		$pdoCheck = '<i class="fa fa-check text-success"></i> PASS';
	} else {
		$pdoCheck = '<i class="fa fa-times text-danger"></i> FAIL';
		$canInstall = 'false';
		$svrErr	= 'true';
	}

    # check Image Magic Support
	if (class_exists('Imagick')) {
		$imagickCheck = '<i class="fa fa-check text-success"></i> PASS';
	} else {
		$imagickCheck = '<i class="fa fa-times text-danger"></i> FAIL';
		//$canInstall = 'false';
		//$svrErr	= 'true';
	}
	// if (function_exists('imagecreatefrompng')) {
	// 	$haspng = '<i class="fa fa-check text-success"></i> PASS';
	// } else {
	// 	$haspng = '<i class="fa fa-times text-danger"></i> FAIL';
	// 	$canInstall = 'false';
	// 	$svrErr	= 'true';
	// }
	if (function_exists('fgetcsv')) {
		$hascsv = '<i class="fa fa-check text-success"></i> PASS';
	} else {
		$hascsv = '<i class="fa fa-times text-danger"></i> FAIL';
		$canInstall = 'false';
		$svrErr	= 'true';
	}

	// Check if the following Directories are writeable
	$installDir = substr(sprintf('%o', fileperms('../')), -4);
    //$uploadsDir = substr(sprintf('%o', fileperms('../application/uploads')), -4);


	if ($installDir >= '0755') {
		$installDirWritable = '<i class="fa fa-check text-success"></i> WRITEABLE';
	} else {
		$installDirWritable = '<i class="fa fa-times text-danger"></i> NOT WRITEABLE';
		$canInstall = 'false';
		$folderErr = 'true';
	}

    if(is_writable('../application/uploads')){
        $uploadDirWritable = '<i class="fa fa-check text-success"></i> WRITEABLE';
    }else{
        $hasMain = '<i class="fa fa-times text-danger"></i> NOT WRITEABLE';
		$canInstall = 'false';
		$folderErr = 'true';
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

            <div class="panel panel-warning mt-10">
    				<div class="panel-heading">Pre-Installation Check</div>
    				<div class="panel-body">
    					<p class="text-warning"><strong>Before we get started, we need to check that your server supports all needed functions to run Funi<sup>1.0</sup>.</strong></p>
    					<table class="table">
    						<tbody>
    							<tr>
    								<th style="width:33.333%;">PHP Version</th>

    								<th class="text-right" style="width:33.333%;">RESULT</th>
    							</tr>
    							<tr>
    								<td data-th="PHP Version">V 7.0+ Required ( <span style="color:#FFAB68">Your version : <?php echo $phpversion; ?></span> )</td>
    								<td class="text-right" data-th="Pass / Fail"><?php echo $phpcheck; ?></td>
    							</tr>
    						</tbody>
    					</table>

    					<table class="table">
    						<tr>
    							<th style="width:50%;">PHP Base Functions</th>
    							<th class="text-right" style="width:50%;">RESULT</th>
    						</tr>
    						<tr>
    							<td>PDO Support</td>
    							<td class="text-right"><?php echo $pdoCheck; ?></td>
    						</tr>
    						<tr>
    							<td>Imagick Support ( Not Mandatory )</td>
    							<td class="text-right"><?php echo $imagickCheck; ?></td>
    						</tr>

    						<tr>
    							<td>CSV File Support</td>
    							<td class="text-right"><?php echo $hascsv; ?></td>
    						</tr>
    					</table>

    					<p class="text-warning">
    						<strong>We also need to check if some neccesary directories/folders are writeable (CHMOD numeric value 0755).</strong>
    						<small class="text-muted">Network Solutions has some good info and instructions about CHMOD <a href="http://www.networksolutions.com/support/how-to-change-file-or-directory-permissions-via-ftp/" target="_blank">here</a>.</small>
    					</p>

    					<table class="table">
    						<tr>
    							<th style="width:50%;">Directory/Folder Name</th>
    							<th class="text-right" style="width:50%;">RESULT</th>
    						</tr>
    						<tr>
    							<td>Install Directory</td>
    							<td class="text-right"><?php echo $installDirWritable; ?></td>
    						</tr>

                            <tr>
    							<td>Uploads Directory</td>
    							<td class="text-right"><?php echo $uploadDirWritable; ?></td>
    						</tr>
    					</table>

    					<?php if ($canInstall == 'true') { ?>
    						<span class="pull-right">
    							<form action="./step1" method="post">
    								<button type="input" name="submit" value="nextStep" class="btn btn-success btn-icon mt-5"><i class="fa fa-check"></i> Start Installation</button>
    							</form>
    						</span>
    					<?php
    						} else {
    							if ($svrErr == 'true') {
    								echo '<div class="alert alert-warning" role="alert">Looks like your server configuration is not compatible with Funi1.0. Please contact your Web Host to see if they can help.</div>';
    							}
    							if ($folderErr == 'true') {
    								echo '<div class="alert alert-warning" role="alert">Looks like one or more of the Directories/Folders is not writeable. Please CHMOD the directory to 0755 to continue the installation.</div>';
    							}
    						}
    					?>
    				</div>
    			</div>

        </div>


    </div>
</body>
</html>
