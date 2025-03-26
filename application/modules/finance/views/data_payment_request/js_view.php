<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/finance/data_payment_request/';
var opsForm = 'form#frmInputData';
var locate = 'table.pr-list';
var dlocate = 'table.dpr-list';
var wcount = 0; //for payment request list row identify
var plocate = 'table.pr-log-list';
var pdlocate = 'table.dpr-log-list';
var pwcount = 0; //for payment request log list row identify

<?php if  (_USER_ACCESS_LEVEL_VIEW == "1") { ?>
$.validator.addMethod("greaterThan", function(value, element, params) {
	if($(params).val() !== value){
		var d1parts = $(params).val().split("-");
		var d2parts = value.split("-");

		if (!/Invalid|NaN/.test(new Date(d2parts[2], d2parts[1] - 1, d2parts[0]))) {
			return new Date(d2parts[2], d2parts[1] - 1, d2parts[0]) > new Date(d1parts[2], d1parts[1] - 1, d1parts[0]);
		}

		return isNaN(value) && isNaN($(params).val()) || (Number(d2parts[2]+'-'+d2parts[1]+'-'+d2parts[0]) > Number(d1parts[2]+'-'+d1parts[1]+'-'+d1parts[0])); 
	} else {
		return true;
	}
},'Must equal or greater than Order Date.');

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
		  { "sClass": "text-center", "aTargets": [ 0,1 ] },
		  { "sClass": "dt-body-right", "aTargets": [ 7 ] }
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
				id_project: {
					required: true
				},
				id_wbs: {
					required: true
				}
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
	<?php } ?>

	<?php if  (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
    //check all
    $("#check-all").click(function () {
        $(".data-check").prop('checked', $(this).prop('checked'));
    });
	<?php } ?>
})

<?php $this->load->view(_TEMPLATE_PATH . "common_module_js"); ?>
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
					$('[name="pr"]').val(data.pr);
					$('select#for_type').val(data.for_type).trigger('change.select2');
					$('.supp_field').hide();
					$('.balance_field').hide();
					$('[name="total_pr"]').prop("readonly", true);
					if(data.for_type=='INVOICE'){
						$('.supp_field').show();
					}
					$.ajax({type: 'post',url: module_path+'/getforidinfo',data: { sel:data.for_type },success: function (response) {
							$('select#id_for').html(response);
							$('select#id_for').val(data.id_for).trigger('change.select2');
						}
					});
					$.ajax({type: 'post',url: module_path+'/getpaytoinfo',data: { sel:data.for_type },success: function (response) {
							$('select#id_payto').html(response);
							$('select#id_payto').val(data.id_payto).trigger('change.select2');
						}
					});
					$('select#id_project_select').val(data.id_project).trigger('change.select2');
					$('[name="id_project"]').val(data.id_project);
					$('[name="project_title"]').val(data.project_title);
					$('select#id_wbs').val(data.id_wbs).trigger('change.select2');
					$('[name="description"]').val(data.description);
					$('[name="notes"]').val(data.notes);
					$('[name="old_status"]').val(data.last_status);
					$('[name="new_status"]').val(data.last_status);
					$('select#last_status').val(data.last_status).trigger('change.select2');
					$('[name="date_request"]').val(data.drequest);
					$('[name="total_pr"]').number(true,0,',','.');
					$('[name="total_pr"]').val(data.trequest);
					$('[name="trf_type"][value="'+data.trf_type+'"]').prop('checked', true);
					$('[name="norek"]').val(data.norek);
					$('[name="namarek"]').val(data.namarek);
					var lstatus = data.last_status;
					if(data.for_type=='CA'){
						$('.balance_field').show();
						$('[name="total_pr"]').prop("readonly", false);
						if(lstatus<2){
							setremainingcabudget(data.id_for);
						}
					}
					$.ajax({type: 'post',url: modloc+'genpaymentreqrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
						expenseviewadjust(lstatus);
					});
					$.ajax({type: 'post',url: modloc+'genlogstatusrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(plocate+' tbody').html(obj[0]);
							pwcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(plocate);
					});
					formviewadjust(data.last_status);
					setact(data.id,data.id_project);
					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){
					$('[name="id"]').val(data.id);
					$('span.pr').html(data.pr);
					$('span.fortype').html(data.for_type);
					$('.supp_field').hide();
					if(data.for_type=='INVOICE'){
						$('.supp_field').show();
					}
					$('span.noref').html(data.noref);
					$('span.project').html(data.project);
					$('span.project_title').html(data.project_title);
					$('span.wbs').html(data.wbs);
					$('span.description').html(data.description);
					$('span.notes').html(data.notes);
					$('span.status').html(data.status);
					$('span.drequest').html(data.drequest);
					$('span.requestor').html(data.requestor);
					$('span.wrequest').html($.number(data.trequest,0,',','.'));
					$('span.trf_type').html(data.trf_type);
					$('span.norek').html(data.norek);
					$('span.namarek').html(data.namarek);
					$.ajax({type: 'post',url: modloc+'genpaymentreqrow',data: { id:data.id,view:true },success: function (response) {
							var obj = JSON.parse(response);
							$(dlocate+' tbody').html(obj[0]);
						}
					});
					$.ajax({type: 'post',url: modloc+'genlogstatusrow',data: { id:data.id,view:true },success: function (response) {
							var obj = JSON.parse(response);
							$(pdlocate+' tbody').html(obj[0]);
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
$( "#btnAddData" ).on('click', function(){
	$('[name="as_draft"]').val('0');
	$('select#last_status').val('1').trigger('change.select2');
	$('.supp_field').hide();
	$('.balance_field').hide();
	$('[name="total_remaining"]').val('0');
	$('[name="total_pr"]').prop("readonly", true);
	setinitform();
	setact('','');
	wcount = 0;
	pwcount = 0;
	$(locate+' tbody').html('');
	$(plocate+' tbody').html('');
});

$('#frmInputData').on('change', 'select#for_type', function(event){
	var id= $(this).val();
	$('.supp_field').hide();
	$('.balance_field').hide();
	$('[name="total_pr"]').prop("readonly", true);
	if(id=='INVOICE'){
		$('.supp_field').show();
	}
	if(id=='CA'){
		$('.balance_field').show();
		$('[name="total_pr"]').prop("readonly", false);
	}
	$.ajax({type: 'post',url: module_path+'/getpaytoinfo',data: { sel:id },success: function (response) {
			$('select#id_payto').html(response);
			$('select#id_payto').val('').trigger('change.select2');
		}
	});
	$.ajax({type: 'post',url: module_path+'/getforidinfo',data: { sel:id },success: function (response) {
			$('select#id_for').html(response);
			$('select#id_for').val('').trigger('change.select2');
			$('select#id_project_select').val('').trigger('change.select2');
			$('[name="project_title"]').val('');
			$('select#id_wbs').val('').trigger('change.select2');
			$('[name="total_pr"]').number(true,0,',','.');
			$('[name="total_pr"]').val(0);
		}
	});
});

$('#frmInputData').on('change', 'select#id_for', function(event){
	var id= $(this).val();
	var tipe= $('select#for_type').val();
	var project_id = '';
	$.ajax({type: 'post',url: module_path+'/getforiddetailinfo',data: { sel:id, type:tipe },success: function (response) {
			var obj = JSON.parse(response);
			project_id = obj.id_project;
			$('select#id_project_select').val(project_id).trigger('change.select2');
			$('[name="id_project"]').val(project_id);
			$('[name="project_title"]').val(obj.project_title);
			$('select#id_wbs').val(obj.id_wbs).trigger('change.select2');
			$('[name="total_pr"]').number(true,0,',','.');
			$('[name="total_pr"]').val(obj.wrequest);
		}
	}).done(function() {
		if(tipe=='CA'){
			setremainingcabudget(id);
		}
		if(project_id){
			setact('',project_id);
		} else {
			setact('','');
		}
	});
});

$('#frmInputData').on('change', 'select#id_project', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getprojectinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="project_title"]').val(obj.project_title);
		}
	});
});

$('#frmInputData').on('change', 'select#id_payto', function(event){
	var id= $(this).val();
	var tipe= $('select#for_type').val();
	$.ajax({type: 'post',url: module_path+'/getrequestorinfo',data: { sel:id, type:tipe },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="norek"]').val(obj.norek);
			$('[name="namarek"]').val(obj.namarek);
		}
	});
});

function setact(id,pid){
	$.ajax({type: 'post',url: modloc+'genactbutton',data: { sel:id, psel:pid },success: function (response) {
		var obj = JSON.parse(response);
		$('span.act-container-btn').html(obj);
		}
	});
}

function setinitform(){
	$('[name="as_draft"]').val('0');
	$('#for_type').prop('disabled', false);
	$('#id_for').prop('disabled', false);
	$('#id_payto').prop('disabled', false);
	$('#id_wbs').prop('disabled', false);
	$('#description').prop('disabled', false);
	$('#notes').prop('disabled', false);
	$('#last_status').prop('disabled', 'disabled');
	$('[name="trf_type"]').attr('disabled', false);
	$('.tools').show();
	$('.ibtnDel').show();
	$('.new-stat').hide();
}

function formviewadjust(last_status){
	if(last_status<2){
		setinitform();
	} else {
		$('#for_type').prop('disabled', 'disabled');
		$('#id_for').prop('disabled', 'disabled');
		$('#id_payto').prop('disabled', 'disabled');
		$('.balance_field').hide();
		$('[name="total_pr"]').prop("readonly", true);
		$('#id_wbs').prop('disabled', 'disabled');
		$('#description').prop('disabled', 'disabled');
		$('#notes').prop('disabled', 'disabled');
		$('#last_status').prop('disabled', 'disabled');
		$('[name="trf_type"]').attr('disabled',true);
		$('.tools').hide();
		$('.ibtnDel').hide();
		if(last_status<7){
			$('.new-stat').show();
		} else {
			$('.new-stat').hide();
		}
	}
}

function expenseviewadjust(last_status){
	if(last_status<2){
		$('.qidescription').prop('disabled', false);
		$('.attcfile').show();
		$('.ibtnDel').show();
	} else {
		$('.qidescription').prop('disabled', 'disabled');
		$('.attcfile').hide();
		$('.ibtnDel').hide();
	}
}

function savedraft()
{
	$('[name="as_draft"]').val('1');
	save();
}

function saveapprv(nstat)
{
	$('[name="new_status"]').val(nstat);
	save();
}

$(opsForm).on('keyup', '[name="total_pr"]', function(event){
	$(this).number(true,0,',','.');
	updatepr();
});

// BOF operation
$("#addprrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genpaymentreqrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			wcount++;
		}
	}).done(function() {
		tSawBclear('table.order-list');
		$('.date-picker').datepicker({
			rtl: App.isRTL(),
			autoclose: true,
			clearBtn: true,
			todayHighlight: true
		});
	});
});

$(locate).on("click", ".ibtnDel", function (event) {
	var row = $(this).attr('data-id');
	var file = $('a.attcfile_'+row).text()
	if(file){
		var id = $('[name="id"]').val();
		$.ajax({type: 'post',url: modloc+'rmattc',data: { id:id,file:file },success: function (response) {}});
	}
	$(this).closest("tr").remove();
});
// EOF operation

// BOF calculate for CA
function setremainingcabudget(id){
	$.ajax({type: 'post',url: modloc+'getremainingcabudget',data: { sel:id },success: function (response) {
		var obj = JSON.parse(response);
			$(opsForm+' [name="total_remaining"]').number(true,0,',','.');
			$(opsForm+' [name="total_remaining"]').val(obj.remainingbudget);
		}
	}).done(function() {
		updatepr();
	});
}

function updatepr()
{
	expire();
	var remaining = goFloat($(opsForm+' [name="total_remaining"]').val());
	var req = goFloat($(opsForm+' [name="total_pr"]').val());
	if((remaining-req) < 0){
		$(opsForm+' [name="total_pr"]').number(true,0,',','.');
		$(opsForm+' [name="total_pr"]').val(remaining);
	}
}
// EOF calculate
<?php } ?>
</script>