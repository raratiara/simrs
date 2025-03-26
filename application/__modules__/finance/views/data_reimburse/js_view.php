<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/finance/data_reimburse/';
var opsForm = 'form#frmInputData';
var locate = 'table.reim-list';
var dlocate = 'table.dreim-list';
var wcount = 0; //for reimburse list row identify
var plocate = 'table.reim-log-list';
var pdlocate = 'table.dreim-log-list';
var pwcount = 0; //for reim log list row identify

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
		  { "sClass": "dt-body-right", "aTargets": [ 7,8 ] }
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
					$('[name="reim"]').val(data.reim);
					$('select#id_project').val(data.id_project).trigger('change.select2');
					$('[name="project_title"]').val(data.project_title);
					$('select#id_wbs').val(data.id_wbs).trigger('change.select2');
					$('[name="description"]').val(data.description);
					$('[name="old_status"]').val(data.last_status);
					$('[name="new_status"]').val(data.last_status);
					$('select#last_status').val(data.last_status).trigger('change.select2');
					$('[name="date_request"]').val(data.drequest);
					$('select#id_requestor').val(data.id_requestor).trigger('change.select2');
					$('[name="nik"]').val(data.nik);
					$('[name="id_dept"]').val(data.id_dept);
					$('[name="departemen"]').val(data.departemen);
					$('[name="total_reim"]').number(true,0,',','.');
					$('[name="total_reim"]').val(data.trequest);
					$('[name="trf_type"][value="'+data.trf_type+'"]').prop('checked', true);
					$('[name="norek"]').val(data.norek);
					$('[name="namarek"]').val(data.namarek);
					var lstatus = data.last_status;
					$.ajax({type: 'post',url: modloc+'genexpensesrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							$(locate+' input.qty').number(true,0,',','.');
							$(locate+' input.price').number(true,0,',','.');
							$(locate+' input.jumlah').number(true,0,',','.');
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
						expenseviewadjust(lstatus);
						$('.date-picker').datepicker({
							rtl: App.isRTL(),
							autoclose: true,
							clearBtn: true,
							todayHighlight: true
						});
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
					$('span.reim').html(data.reim);
					$('span.project').html(data.project);
					$('span.project_title').html(data.project_title);
					$('span.wbs').html(data.wbs);
					$('span.description').html(data.description);
					$('span.status').html(data.status);
					$('span.drequest').html(data.drequest);
					$('span.requestor').html(data.requestor);
					$('span.nik').html(data.nik);
					$('span.departemen').html(data.departemen);
					$('span.wrequest').html($.number(data.trequest,0,',','.'));
					$('span.trf_type').html(data.trf_type);
					$('span.norek').html(data.norek);
					$('span.namarek').html(data.namarek);
					$.ajax({type: 'post',url: modloc+'genexpensesrow',data: { id:data.id,view:true },success: function (response) {
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
	setinitform();
	setact('','');
	wcount = 0;
	pwcount = 0;
	$(locate+' tbody').html('');
	$(plocate+' tbody').html('');
});

$('#frmInputData').on('change', 'select#id_project', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getprojectinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_requestor').val(obj.id_pic).trigger('change.select2');
			$('[name="project_title"]').val(obj.project_title);
			$('[name="nik"]').val(obj.nik);
			$('[name="id_dept"]').val(obj.id_dept);
			$('[name="departemen"]').val(obj.departemen);
			$('[name="norek"]').val(obj.norek);
			$('[name="namarek"]').val(obj.namarek);
		}
	}).done(function() {
		if(id){
			setact('',id);
		} else {
			setact('','');
		}
	});
});

$('#frmInputData').on('change', 'select#id_requestor', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getrequestorinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="nik"]').val(obj.nik);
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
	$('#id_project').prop('disabled', false);
	$('#id_requestor').prop('disabled', false);
	$('#id_wbs').prop('disabled', false);
	$('#description').prop('disabled', false);
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
		$('#id_project').prop('disabled', 'disabled');
		$('#id_requestor').prop('disabled', 'disabled');
		$('#id_wbs').prop('disabled', 'disabled');
		$('#description').prop('disabled', 'disabled');
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
		$('.qty').prop('disabled', false);
		$('.satuan').prop('disabled', false);
		$('.price').prop('disabled', false);
		$('.jumlah').prop('disabled', false);
		$('.post_budget').prop('disabled', false);
		$('.attcfile').show();
		$('.picker').show();
		$('.ibtnDel').show();
	} else {
		$('.qidescription').prop('disabled', 'disabled');
		$('.qty').prop('disabled', 'disabled');
		$('.satuan').prop('disabled', 'disabled');
		$('.price').prop('disabled', 'disabled');
		$('.jumlah').prop('disabled', 'disabled');
		$('.post_budget').prop('disabled', 'disabled');
		$('.attcfile').hide();
		$('.picker').hide();
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

// BOF operation
$("#addreimrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genexpensesrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			wcount++;
			$(locate+' [name="qty['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="price['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="jumlah['+wcount+']"]').number(true,0,',','.');
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
	updatetotal();
});
// EOF operation
// BOF calculate
function updatetotal()
{
	expire();
	var srow = $(locate+' input.jumlah').length;
	var oGross = 0;
	for(var i = 0; i < srow; i++) {
		oGross += goFloat($(locate+' input.jumlah').eq(i).val());
	}
	$(opsForm+' [name="total_reim"]').number(true,0,',','.');
	$(opsForm+' [name="total_reim"]').val(oGross);
}

// Adjust & calculate row price, subtotal
function rowcalc(el)
{
	var oPrice =  goFloat($(locate+' [name="price['+el+']"]').val());
	var rQty = goFloat($(locate+' [name="qty['+el+']"]').val());
	var oSubtotal = oPrice*rQty;
	$(locate+' [name="jumlah['+el+']"]').number(true,0,',','.');
	$(locate+' [name="jumlah['+el+']"]').val(oSubtotal);
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

// EOF calculate
<?php } ?>
</script>