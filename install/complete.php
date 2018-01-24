<?php

    session_start();


    if(!isset($_SESSION['finish'])){
        header('Location: index');
        exit;
    }

    //session_unset();

     if(isset($_POST['submit'])){
        //foreach (scandir('./') as file) {
        //    # delete all files in dir
        //    unlink(file);
        //}

        // # delete install directory
        // unlink('./');


        # rename install folder
        rename('../install', '../setUpDir');

        # rename funi1.0 dir to appName
        rename('../../funi1.0', '../../' . $_SESSION['appName']);

        $appName = $_SESSION['appName'];

        session_unset();

        # redirect to app
        header('Location: http://' . $_SERVER['HTTP_HOST'] . '/' . $appName . '/public/');
        exit;
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
     			Done! Installation Complete
     		</div>

            <div class="panel panel-warning mt-10">
                    <div class="panel-heading">Installation Complete</div>
                    <div class="panel-body">

                        <p class="text-warning"><strong>Your Installation is complete. Here are a few Tip on how best to use Funi<sup>1.0</sup>.</strong></p>

                        <table class="table">
    						<tbody>

                                <tr>
    								<td>You Project Folder will be renamed to <?php echo $_SESSION['appName']; ?>.</td>
    							</tr>
    							<tr>
    								<td>All your Application codes must be put inside the application directory. Treat other Directories like a BLACK BOX</td>
    							</tr>

                                <tr>
    								<td>Follow Naming conventions. Model files have the extention .model.php, class file - classname.class.php, controller files - filename.controller.php etc</td>
    							</tr>

                                <tr>
    								<td>You can set up you live paramters when you are ready to take up application live. Go to http://<?php echo $_SERVER['HTTP_HOST'] . '/' . $_SESSION['appName']; ?>/deployment to do this</td>
    							</tr>

                                <tr>
    								<td>HACK</td>
    							</tr>

    						</tbody>
    					</table>

                        <span class="pull-right">
                            <form action="./complete" method="post">
                                <button type="input" name="submit" value="nextStep" class="btn btn-info btn-icon mt-20" ><i class="fa fa-check"></i> Begin</button>
                            </form>
                        </span>

                    </div>
                </div>

        </div>


    </div>
 </body>
 </html>
