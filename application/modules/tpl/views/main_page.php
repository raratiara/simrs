<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title><?php echo _TITLE; ?></title>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta content="width=device-width, initial-scale=1" name="viewport" />
    <meta content="" name="description" />
    <meta content="" name="author" />
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700&subset=all" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/simple-line-icons/simple-line-icons.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/uniform/css/uniform.default.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-switch/css/bootstrap-switch.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/datatables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/bootstrap/datatables.bootstrap.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/colreorder/colReorder.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/fixedheader/fixedHeader.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/responsive/responsive.dataTables.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-timepicker/css/bootstrap-timepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-toastr/toastr.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_PLUGINS; ?>chosen/chosen.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_PLUGINS; ?>bootstrap-multiselect/css/bootstrap-multiselect.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_PLUGINS; ?>eonasdan-bootstrap-datetimepicker/css/bootstrap-datetimepicker.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_PLUGINS; ?>tablesaw-stackonly/tablesaw.stackonly.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>/plugins/select2/css/select2.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/select2/css/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>css/components.min.css" rel="stylesheet" id="style_components" type="text/css" />
    <link href="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>css/plugins.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_LAYOUTS_METRONIC_TEMPLATE; ?>layout/css/layout.min.css" rel="stylesheet" type="text/css" />
    <link href="<?php echo _ASSET_LAYOUTS_METRONIC_TEMPLATE; ?>layout/css/themes/darkblue.min.css" rel="stylesheet" type="text/css" id="style_color" />
    <link href="<?php echo _ASSET_LAYOUTS_METRONIC_TEMPLATE; ?>layout/css/custom.min.css" rel="stylesheet" type="text/css" />
    <link href="<?= base_url('css/custom.css') ?>" rel="stylesheet" type="text/css" />
    <?php
    // inline css related to page. can multiple
    if (isset($css)) {
        if (is_array($css)) {
            foreach ($css as $script) {
                $this->load->view($script);
            }
        } else {
            $this->load->view($css);
        }
    }
    ?>
	
    <link rel="shortcut icon" href="favicon.ico" />
</head>

<body class="page-header-fixed page-sidebar-closed-hide-logo page-content-white">
    <div class="page-header navbar navbar-fixed-top">
        <div class="page-header-inner ">
            <div class="page-logo">
                <a href="<?= base_url() ?>">
                    <img src="<?php echo _ASSET_LOGO_INSIDE; ?>" alt="logo" style="height:30px;" class="logo-default" /> </a>
                <div class="menu-toggler sidebar-toggler"> </div>
            </div>
            <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse"> </a>
            <?php $this->load->view(_TEMPLATE_PATH . "navbar"); ?>
        </div>
    </div>
    <div class="clearfix"> </div>
    <div class="page-container">
        <?php $this->load->view(_TEMPLATE_PATH . "sidebar"); ?>
        <div class="page-content-wrapper">
            <div class="page-content">
                <div class="page-bar">
                    <ul class="page-breadcrumb">
                        <?php if (isset($breadcrumb) && $breadcrumb) : ?>
                        <?php if (is_array($breadcrumb)) : ?>
                        <li>
                            <i class="fa fa-home"></i>
                            <a href="<?= base_url() ?>">Home</a>
                            <i class="fa fa-circle"></i>
                        </li>
                        <?php foreach ($breadcrumb as $i => $b) : ?>
                        <li>
                            <span><?= $b ?></span>
                            <?php if ($i < count($breadcrumb) - 1) : ?> <i class="fa fa-circle"></i>
                        </li>
                        <?php endif; ?>
                        <?php endforeach ?>
                        <?php else : ?>
                        <?= $breadcrumb ?>
                        <?php endif ?>
                        <?php endif; ?>
                    </ul>
                </div>
                <?php if (isset($sview)) $this->load->view($sview); ?>
            </div>
        </div>
    </div>

    <?php if ($this->session->flashdata('msg')) : ?>

    <div id="_info_" class="modal fade" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-blue bg-font-blue no-padding">
                    <div class="table-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">
                            <span class="white">&times;</span>
                        </button>
                        Notifikasi
                    </div>
                </div>

                <div class="modal-body" style="height:100px;">
                    <div class="alert alert-<?= $this->session->flashdata('stats') == '0' ? 'error' : 'info' ?>">
                        <p class="err-form" style="letter-spacing: 1px;"><?php echo strtoupper($this->session->flashdata('msg')); ?></p>
                    </div>
                </div>

                <div class="modal-footer no-margin-top">
                    <button class="btn btn-sm btn-danger pull-right" data-dismiss="modal">
                        <i class="ace-icon fa fa-times"></i>
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php endif; ?>

    <?php $this->load->view(_TEMPLATE_PATH . "footer"); ?>
    <!--[if lt IE 9]>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/respond.min.js"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/excanvas.min.js"></script> 
    <![endif]-->
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-hover-dropdown/bootstrap-hover-dropdown.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-slimscroll/jquery.slimscroll.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery.blockui.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/uniform/jquery.uniform.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-switch/js/bootstrap-switch.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/ckeditor/ckeditor.js" type="text/javascript"></script>
	<script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootbox/bootbox.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>scripts/datatable.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/datatables.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/bootstrap/datatables.bootstrap.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/colreorder/dataTables.colReorder.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/fixedheader/dataTables.fixedHeader.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/datatables/plugins/responsive/dataTables.responsive.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-datepicker/js/bootstrap-datepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/moment.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery.scrollTo.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PLUGINS; ?>chosen/chosen.jquery.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PLUGINS; ?>bootstrap-multiselect/js/bootstrap-multiselect.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PLUGINS; ?>eonasdan-bootstrap-datetimepicker/js/bootstrap-datetimepicker.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PLUGINS; ?>jquery-number/jquery.number.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PLUGINS; ?>tablesaw-stackonly/tablesaw.stackonly.jquery.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/select2/js/select2.full.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-validation/js/jquery.validate.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/jquery-validation/js/additional-methods.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>plugins/bootstrap-toastr/toastr.min.js" type="text/javascript"></script>
    <script type="text/javascript">
        function aPath() {
            var path = '<?php echo _ASSET_METRONIC_TEMPLATE; ?>';

            return path;
        }
    </script>
    <script src="<?php echo _ASSET_GLOBAL_METRONIC_TEMPLATE; ?>scripts/app.min.js" type="text/javascript"></script>
    <script src="<?php echo _ASSET_PAGES_METRONIC_TEMPLATE; ?>scripts/table-datatables-fixedheader.min.js" type="text/javascript"></script>

    <?php
    // inline javascript related to page. can multiple
    if (isset($js)) {
        if (is_array($js)) {
            foreach ($js as $script) {
                $this->load->view($script);
            }
        } else {
            $this->load->view($js);
        }
    }
    ?>

    <script src="<?php echo _ASSET_LAYOUTS_METRONIC_TEMPLATE; ?>layout/scripts/layout.min.js" type="text/javascript"></script>

    <?php if ($this->session->flashdata('error') == true) : ?>
    <script type="text/javascript">
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 2000);
    </script>
    <?php endif; ?>
    <?php if ($this->session->flashdata('msg') == true) : ?>
    <script type="text/javascript">
        $(window).load(function() {
            $('#_info_').modal('show');
        });
        setTimeout(function() {
            $('#_info_').modal('hide');
        }, 2000);
    </script>
    <?php endif; ?>
</body>

</html> 