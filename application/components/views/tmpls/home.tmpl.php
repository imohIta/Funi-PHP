<?php

    global $registry;
    $config = $registry->get('config');

 ?>
<html>
    <head>
        <title> <?php echo $config->get('appTitle'); ?></title>
        <style>

            body{
                background-color: #F0F0F0;
            }

            #container{
                width: 80%;
                margin-top:10%;
            }

            .text-holder{
                text-align: center;
                margin: auto;
                width:60%;
                margin-top:10%;
            }

            .logo-txt{
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size:15em;
                font-weight: 600;
                line-height: 0.5;
                color: #777;
            }

            .logo-txt-sub{
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size:40px;
                color: #777;
            }

            .logo-txt-sm{
                font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
                font-size:3em;
                line-height: 0.2;
                color: #777;
            }

            .text-center{ text-align:center; }

        </style>

        <script type="text/javascript">

            function sivamtime() {
                now = new Date();
                hour = now.getHours();
                min = now.getMinutes();
                sec = now.getSeconds();

                if (min<=9) { min="0"+min; }
                if (sec<=9) { sec="0"+sec; }
                if (hour>12) { hour=hour-12; add="PM"; }
                else { hour=hour; add="AM"; }
                if (hour==12) { add="PM"; }

                time = ((hour<=9) ? "0"+hour : hour) + ":" + min + ":" + sec + " " + add;

                if (document.getElementById) { document.getElementById('theTime').innerHTML = time; }
                else if (document.layers) {
                 document.layers.theTime.document.write(time);
                 document.layers.theTime.document.close(); }

                setTimeout("sivamtime()", 1000);
            }
            window.onload = sivamtime;


        </script>
    </head>
    <body>

        <div class="container">

            <div class="text-holder">

                <p><span class="logo-txt">Funi</span><sup class="logo-txt-sm">1.0<sup></p>
                <p class="logo-txt-sm">PHP FRAMEWORK</p>

                <br />
                <hr style="color:#f5f5f5" />
                <br >

                <p class="text-center logo-txt-sub" style="color:#999"><?php echo date('jS F Y'); ?></p>
                <h1 class="text-center logo-txt-sm" id="theTime" style="color:#999; font-size:60px"></h1>

            </div>



       </div>

    </body>
</html>
