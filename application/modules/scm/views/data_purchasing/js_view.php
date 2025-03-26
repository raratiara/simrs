<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/scm/data_purchasing/';
var opsForm = 'form#frmInputData';
var locate = 'table.order-list';
var dlocate = 'table.dorder-list';
var wcount = 0; //for item list row identify
var pout = false; //for printing

<?php if  (_USER_ACCESS_LEVEL_VIEW == "1") { ?>
jQuery(function($) {
	/* load table list */
	myTable =
	$('#dynamic-table')
	.DataTable( {
		fixedHeader: {
			headerOffset: $('.page-header').outerHeight()
		},
		responsive: true,
		bAutoWidth: false,
		"aoColumnDefs": [
		  { "bSortable": false, "aTargets": [ 0,1 ] },
		  { "sClass": "text-center", "aTargets": [ 0,1 ] }
		],
		"aaSorting": [
		  	[2,'asc'] 
		],
		"sAjaxSource": module_path+"/get_data",
		"bProcessing": true,
        "bServerSide": true,
		"pagingType": "bootstrap_full_number",
		"colReorder": true
    } );

	<?php if  (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1") { ?>
	validator = $("#frmInputData").validate({
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: false, // do not focus the last invalid input
		ignore: "", // validate all fields including form hidden input
		rules: {
			id_po: {
				required: true
			},
			delivery_date: {
				required: true
			},
		},
		messages: { // custom messages for radio buttons and checkboxes
		},
		errorPlacement: function (error, element) { // render error placement for each input type
			if (element.parent(".input-group").size() > 0) {
				error.insertAfter(element.parent(".input-group"));
			} else if (element.attr("data-error-container")) { 
				error.appendTo(element.attr("data-error-container"));
			} else if (element.parents('.radio-list').size() > 0) { 
				error.appendTo(element.parents('.radio-list').attr("data-error-container"));
			} else if (element.parents('.radio-inline').size() > 0) { 
				error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
			} else if (element.parents('.checkbox-list').size() > 0) {
				error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
			} else if (element.parents('.checkbox-inline').size() > 0) { 
				error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
			} else {
				error.insertAfter(element); // for other inputs, just perform default behavior
			}
		},
		highlight: function (element) { // hightlight error inputs
			$(element)
				.closest('.form-group').addClass('has-error'); // set error class to the control group
		},
		unhighlight: function (element) { // revert the change done by hightlight
			$(element)
				.closest('.form-group').removeClass('has-error'); // set error class to the control group
		},
		success: function (label) {
			label
				.closest('.form-group').removeClass('has-error'); // set success class to the control group
		}
	});

	//initialize datepicker
	$('.date-picker').datepicker({
		rtl: App.isRTL(),
		autoclose: true,
		clearBtn: true,
		todayHighlight: true
	});
	$('.date-picker .form-control').change(function() {
		$("#frmInputData").validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input 
	})

	CKEDITOR.replaceClass = "txteditor";
    CKEDITOR.config.toolbar = [
        ['Bold', 'Italic', 'Underline', '-', 'Undo', 'Redo'],
        ['Outdent', 'Indent', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock', 'Table'],
        ['Source', 'Maximize', 'ShowBlocks']
    ];
    CKEDITOR.config.height = "100px";
    CKEDITOR.config.baseFloatZIndex = "100001";
    CKEDITOR.config.skin = "moono_blue";
    CKEDITOR.addCss('ul, ol {padding: 0px 0px 0px 15px;}');
    CKEDITOR.bootstrapModalFix = function (modal, $) {
        modal.on('shown', function () {
            var that = $(this).data('modal');
            $(document)
                .off('focusin.modal')
                .on('focusin.modal', function (e) {
                    // Add this line
                    if (e.target.className && e.target.className.indexOf('cke_') == 0) return;

                    // Original
                    if (that.$element[0] !== e.target && !that.$element.has(e.target).length) {
                        that.$element.focus()
                    }
                });
        });
    };
    CKEDITOR.bootstrapModalFix($('#modal-form-data'), $);
    $.fn.modal.Constructor.prototype.enforceFocus = function () {
        $(document)
            .off('focusin.bs.modal') // guard against infinite focus loop
            .on('focusin.bs.modal', $.proxy(function (e) {
                if (
                    this.$element[0] !== e.target && !this.$element.has(e.target).length
                    // CKEditor compatibility fix start.
                    && !$(e.target).closest('.cke_dialog, .cke').length
                    // CKEditor compatibility fix end.
                ) {
                    this.$element.trigger('focus');
                }
            }, this));
    };
	$('span.act-container-btn').html('<button class="btn btn-info" id="submit-data" onclick="saveme()"><i class="fa fa-check"></i> Save </button><button class="btn" onclick="reset()"><i class="fa fa-undo"></i> Reset </button>');
	<?php } ?>

	<?php if  (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
    //check all
    $("#check-all").click(function () {
        $(".data-check").prop('checked', $(this).prop('checked'));
    });
	<?php } ?>
})

<?php $this->load->view(_TEMPLATE_PATH . "common_module_print_js"); ?>
<?php $this->load->view(_TEMPLATE_PATH . "numbers2words"); ?>
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_UPDATE == "1" || _USER_ACCESS_LEVEL_DETAIL == "1")) { ?>
function load_data()
{
    $.ajax({
		type: "POST",
        url : module_path+'/get_detail_data',
		data: { id: idx },
		cache: false,		
        dataType: "JSON",
        success: function(data)
        {
			if(data != false){
				if(save_method == 'update'){
					$('[name="id"]').val(data.id);
					$('select#id_po').val(data.id_po).trigger('change.select2');
					$('select#id_project').val(data.id_project).trigger('change.select2');
					$('select#id_supplier').val(data.id_supplier).trigger('change.select2');
					$('[name="supplier_name"]').val(data.supplier_name);
					$('[name="supplier_address"]').val(data.supplier_address);
					$('[name="pic"]').val(data.supplier_pic);
					$('[name="date"]').val(data.dt);
					$('[name="delivery_date"]').val(data.ddt);
					$('[name="id_currency"][value="'+data.id_currency+'"]').prop('checked', true);
					$('select#id_spk').val(data.id_spk).trigger('change.select2');
					$('[name="worth"]').number(true,0,',','.');
					$('[name="worth"]').val(data.worth);
					$('select#id_warehouse').val(data.id_warehouse).trigger('change.select2');
					$('[name="warehouse_address"]').val(data.warehouse_address);
					if(data.top){
						var top = JSON.parse(data.top);
						$('[name="top_dp"]').val(top.top_dp);
						$('[name="top_t1"]').val(top.top_t1);
						$('[name="top_t2"]').val(top.top_t2);
						$('[name="top_t3"]').val(top.top_t3);
						$('[name="top_tf"]').val(top.top_tf);
						$('[name="top_rt"]').val(top.top_rt);
					}
					$('[name="remark"]').val(data.remark);
					// $('[name="term_condition"]').val(data.term_condition);
					CKEDITOR.instances["term_condition"].setData(data.term_condition);
					$.ajax({type: 'post',url: modloc+'genitemrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							$(locate+' input.qty').number(true,0,',','.');
							$(locate+' input.price').number(true,0,',','.');
							$(locate+' input.subtotal').number(true,2,',','.');
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
					});
					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){
					$('span.po').html(data.po);
					$('span.project').html(data.project);
					$('span.supplier_code').html(data.supplier_code);
					$('span.supplier_name').html(data.supplier_name);
					$('span.supplier_address').html(data.supplier_address);
					$('span.supplier_pic').html(data.supplier_pic);
					$('span.date').html(data.dt);
					$('span.delivery_date').html(data.ddt);
					$('span.currency').html(data.currency);
					$('span.spk').html(data.spk);
					$('span.spk_title').html(data.spk_title);
					$('span.worth').html($.number(data.worth,0,',','.'));
					$('span.warehouse').html(data.warehouse_description);
					$('span.warehouse_kodepos').html(data.warehouse_kodepos);
					$('span.warehouse_address').html(data.warehouse_address);
					var approval = 'Prepared';
					if(data.approval == 1){
						approval = 'Approved';
					}
					$('span.approval').html(approval);
					if(data.top){
						var top = JSON.parse(data.top);
						$('span.top_dp').html(top.top_dp);
						$('span.top_t1').html(top.top_t1);
						$('span.top_t2').html(top.top_t2);
						$('span.top_t3').html(top.top_t3);
						$('span.top_tf').html(top.top_tf);
						$('span.top_rt').html(top.top_rt);
					}
					$('span.remark').html(data.remark);
					$('span.term_condition').html(data.term_condition);
					$.ajax({type: 'post',url: modloc+'genitemrow',data: { id:data.id,view:true },success: function (response) {
							var obj = JSON.parse(response);
							$(dlocate+' tbody').html(obj[0]);
						}
					});
					$('#modal-view-data').modal('show');
				}
			} else {
				title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-exclamation-circle fa-5x" style="color:red"></i></div>';
				btn = '<br/><button class="btn blue" data-dismiss="modal">OK</button>';
				msg = '<p>Gagal peroleh data.</p>';
				var dialog = bootbox.dialog({
					message: title+'<center>'+msg+btn+'</center>'
				});
				if(response.status){
					setTimeout(function(){
						dialog.modal('hide');
					}, 1500);
				}
			}
        },
        error: function (jqXHR, textStatus, errorThrown)
        {
			var dialog = bootbox.dialog({
				title: 'Error ' + jqXHR.status + ' - ' + jqXHR.statusText,
				message: jqXHR.responseText,
				buttons: {
					confirm: {
						label: 'Ok',
						className: 'btn blue'
					}
				}
			});
        }
    });
}
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1")) { ?>
/* additional action */
$('#frmInputData').on('change', 'select#id_supplier', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getsupplierinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="supplier_name"]').val(obj.supplier_name);
			$('[name="supplier_address"]').val(obj.supplier_address);
			$('[name="pic"]').val(obj.pic);
		}
	});
});

$('#frmInputData').on('change', 'select#id_po', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getpoinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_project').val(obj.id_project).trigger('change.select2');
		}
	});
});

$('#frmInputData').on('change', 'select#id_project', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getprojectinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_po').val('').trigger('change.select2');
		}
	});
});

$('#frmInputData').on('change', 'select#id_warehouse', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getwarehouseinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="warehouse_address"]').val(obj.warehouse_address);
		}
	});
});

$( "#btnAddData" ).on('click', function(){
	wcount = 0;
	$(locate+' tbody').html('');
});


// BOF operation
$("#addrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genitemrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			//$(".id_item").chosen({allow_single_deselect: true});
			wcount++;
			$(locate+' [name="qty['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="price['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="subtotal['+wcount+']"]').number(true,2,',','.');
		}
	}).done(function() {
		tSawBclear('table.order-list');
	});
});

$(locate).on('keyup', 'input.qty', function(event){
	var row = $(this).attr('data-id');
	$(this).number(true,0,',','.');
	rowcalc(row);
	updatetotal();
});

$(locate).on('keyup', 'input.price', function(event){
	var row = $(this).attr('data-id');
	$(this).number(true,0,',','.');
	rowcalc(row);
	updatetotal();
});

// BOF calculate
function updatetotal()
{
	expire();
	var srow = $(locate+' input.subtotal').length;
	var oGross = 0;
	for(var i = 0; i < srow; i++) {
		oGross += goFloat($(locate+' input.subtotal').eq(i).val());
	}
	$(opsForm+' [name="worth"]').number(true,0,',','.');
	$(opsForm+' [name="worth"]').val(oGross);
}

// Adjust & calculate row price, subtotal
function rowcalc(el)
{
	var oPrice =  goFloat($(locate+' [name="price['+el+']"]').val());
	var rQty = goFloat($(locate+' [name="qty['+el+']"]').val());
	var oSubtotal = oPrice*rQty;
	$(locate+' [name="subtotal['+el+']"]').number(true,2,',','.');
	$(locate+' [name="subtotal['+el+']"]').val(oSubtotal);
}

// Re-adjust & calculate all
function recalc()
{
	$(locate+' input.qty').each(function() {
		var id= $(this).val()
		var row = $(this).attr("data-id");
		rowcalc(row);
	});
	updatetotal();
	$("#frmInputData").validate();
}

function saveme()
{
    for (var instance in CKEDITOR.instances)
        CKEDITOR.instances[instance].updateElement();
    save();
}
<?php } ?>
</script>