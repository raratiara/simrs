<!DOCTYPE html>
<!--[if IE 8]> <html lang="en" class="ie8 no-js"> <![endif]-->
<!--[if IE 9]> <html lang="en" class="ie9 no-js"> <![endif]-->
<!--[if !IE]><!-->
<html lang="en">
    <!--<![endif]-->
    <!-- BEGIN HEAD -->
    <head>
        <meta charset="utf-8" />
        <title>Login Page - <?php echo _TITLE; ?></title>
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <meta content="" name="description" />
        <meta content="" name="author" />
        <!-- BEGIN GLOBAL MANDATORY STYLES -->
        <link href="http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
        <!-- END GLOBAL MANDATORY STYLES -->
        <!-- BEGIN PAGE LEVEL PLUGINS -->
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
        <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
        <!-- END PAGE LEVEL PLUGINS -->
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
                <img width="109" src="<?php echo _ASSET_LOGO_FRONT; ?>" alt="<?php echo _COMPANY_NAME; ?>" /> </a>
        </div>
        <!-- END LOGO -->
        <!-- BEGIN LOGIN -->
        <div class="content">
            <!-- BEGIN LOGIN FORM -->
            <form id="login-form" class="login-form">
                <h3 class="form-title font-green">Sign In</h3>
                <div class="alert alert-danger display-hide">
                    <button class="close" data-close="alert"></button>
                    <span> Enter any username and password. </span>
                </div>
                <div class="form-group">
                    <!--ie8, ie9 does not support html5 placeholder, so we just show field title for that-->
                    <label class="control-label visible-ie8 visible-ie9">Username</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" placeholder="Username" name="username" /> </div>
                <div class="form-group">
                    <label class="control-label visible-ie8 visible-ie9">Password</label>
                    <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" placeholder="Password" name="userpasswd" /> </div>
                <div class="form-actions">
                    <button type="submit" class="btn green uppercase">Login</button>
                    <label class="rememberme check">
                        <input type="checkbox" name="remember" value="1" />Remember </label>
                </div>
                <div class="create-account">
                    <p>
                        &nbsp;
                    </p>
                </div>
            </form>
            <!-- END LOGIN FORM -->
        </div>
        <div class="copyright"><?php echo _COPYRIGHT; ?></div>
        <!--[if lt IE 9]>
<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/respond.min.js"></script>
<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/excanvas.min.js"></script> 
<![endif]-->
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/js.cookie.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery.blockui.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
		<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
		<script type="text/javascript">
			function aPath() {
				return '<?= _ASSET_METRONIC_TEMPLATE ?>';
			}
		</script>
        <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>scripts/app.min.js" type="text/javascript"></script>
		<script>
		var Login = function() {
			var e = function() {
				$(".login-form").validate({
					errorElement: "span",
					errorClass: "help-block",
					focusInvalid: false,
					rules: {
						username: { required: true },
						userpasswd: { required: true },
						remember: { required: false }
					},
					messages: {
						username: { required: "Username is required." },
						userpasswd: { required: "Password is required." }
					},
					invalidHandler: function(e, r) {
                        $('.alert-danger', $('.login-form')).html('<button class="close" data-close="alert"></button><span>Enter your username and password.</span>');
						$(".alert-danger", $(".login-form")).show()
					},
					highlight: function(e) {
						$(e).closest(".form-group").addClass("has-error")
					},
					success: function(e) {
						e.closest(".form-group").removeClass("has-error"), e.remove()
					},
					errorPlacement: function(e, r) {
						e.insertAfter(r.closest(".input-icon"))
					},
					submitHandler: function(e) {
						//e.submit()
                        $('.alert-danger', $('.login-form')).hide();
                        $.ajax({
                            url: 'login/auth',
                            data: $('#login-form').serialize(),
                            type: "POST",
                            success: function(data) {
                                if (data == 'Welcome') {
									window.location.href = '<?= base_url('dashboard') ?>';
                                } else {
                                    $('.alert-danger', $('.login-form')).html(data);
                                    $('.alert-danger', $('.login-form')).show();
									$('.login-form [name="username"]').val('');
									$('.login-form [name="userpasswd"]').val('');
                                }
                            }
                        })

                        return false;
					}
				}),
				$(".login-form input").keypress(function(e) {
					return 13 == e.which ? ($(".login-form").validate().form() && $(".login-form").submit(), !1) : void 0
				})
			};
			return {
				init: function() {
				  e()
				}
			}
		}();
		jQuery(document).ready(function() {
			Login.init()
		});
		</script>
    </body>
</html>