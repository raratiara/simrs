                <div class="top-menu">
                    <ul class="nav navbar-nav pull-right">
                        <!-- BEGIN NOTIFICATION DROPDOWN -->
                        <!-- END NOTIFICATION DROPDOWN -->
                        <!-- BEGIN INBOX DROPDOWN -->
                        <!-- END INBOX DROPDOWN -->
                        <!-- BEGIN TODO DROPDOWN -->
                        <!-- END TODO DROPDOWN -->
                        <!-- BEGIN USER LOGIN DROPDOWN -->
                        <!-- DOC: Apply "dropdown-dark" class after below "dropdown-extended" to change the dropdown styte -->
                        <li class="dropdown dropdown-user">
                            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                                <!--img alt="" class="img-circle" src="<?php echo _ASSET_LAYOUTS_METRONIC_TEMPLATE;?>layout/img/avatar3_small.jpg" /-->
                                <span class="username username-hide-on-mobile">Selamat Datang, <?php echo $_SESSION["name"];?>..</span>
                                <i class="fa fa-angle-down"></i>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-default">
                                <li>
                                    <a href="<?php echo _URL.'reset_password/';?>">
                                        <i class="icon-pencil"></i> Reset Password </a>
                                </li>
                                <li>
                                    <a href="<?php echo _URL.'login/logout';?>">
                                        <i class="icon-key"></i> Log Out </a>
                                </li>
                            </ul>
                        </li>
                        <!-- END USER LOGIN DROPDOWN -->
                        <!-- BEGIN QUICK SIDEBAR TOGGLER -->
                        <!-- END QUICK SIDEBAR TOGGLER -->
                    </ul>
                </div>
