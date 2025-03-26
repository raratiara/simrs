<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>Activation Page - <?php echo _TITLE; ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN THEME GLOBAL STYLES -->
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>css/plugins.min.css" rel="stylesheet" type="text/css" />
        <!-- END THEME GLOBAL STYLES -->
        <!-- BEGIN PAGE LEVEL STYLES -->
        <link href="<?php echo _ASSET_PAGES_METRONIC_TEMPLATE; ?>css/login.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL STYLES -->
        <!-- BEGIN THEME LAYOUT STYLES -->
        <!-- END THEME LAYOUT STYLES -->
        <link rel="shortcut icon" href="favicon.ico" /> </head>
    <!-- END HEAD -->

    <body class=" login">
        <!-- BEGIN LOGO -->
        <div class="logo">
            <a href="/">
                <img src="<?php echo _ASSET_IMG; ?>logo/cat.png" alt="" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
                <h3 class="form-title font-green">Activation</h3>
 				<?=$message;?>
                <div class="create-account">
                    <p>
                        <a href="/" class="btn btn-info btn-lg uppercase">Login</a>
					</p>
                </div>
        </div>
        <div class="copyright"><?php echo _COPYRIGHT; ?></div>
        <!--[if lt IE 9]>
<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/respond.min.js"></script>
<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/excanvas.min.js"></script> 
<![endif]-->
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    </body>
</html>