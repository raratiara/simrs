<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/purchasing/po/';
var opsForm = 'form#frmInputData';
var locate = 'table.order-list';
var dlocate = 'table.dorder-list';
var wcount = 0; //for item list row identify
var plocate = 'table.attc-list';
var pdlocate = 'table.dattc-list';
var pwcount = 0; //for po list row identify

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
				date_po: {
					required: true
				},
				date_due: {
					required: true
				},
				po: {
					required: true
				},
				id_supplier: {
					required: true
				},
				id_currency: {
					required: true
				},
				id_term: {
					required: true
				},
				id_status: {
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

<?php $this->load->view(_TEMPLATE_PATH . "common_module_print_js"); ?>
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
					$('[name="date_po"]').val(data.dpo);
					$('[name="date_due"]').val(data.ddue);
					$('[name="po"]').val(data.po);
					$('[name="spk"]').val(data.spk);
					$('[name="worth"]').number(true,0,',','.');
					$('[name="worth"]').val(data.worth);
					$('select#id_supplier').val(data.id_supplier).trigger('change.select2');
					$('[name="supplier_name"]').val(data.supplier_name);
					$('[name="supplier_address"]').val(data.supplier_address);
					$('select#id_pic').val(data.id_pic).trigger('change.select2');
					$('[name="id_term"][value="'+data.id_term+'"]').prop('checked', true);
					$('[name="id_currency"][value="'+data.id_currency+'"]').prop('checked', true);
					$('select#id_status').val(data.id_status).trigger('change.select2');
					$('[name="description"]').val(data.description);
					$('[name="notes"]').val(data.notes);
					$.ajax({type: 'post',url: modloc+'genpoitemrow',data: { id:data.id },success: function (response) {
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
					$.ajax({type: 'post',url: modloc+'genattcrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(plocate+' tbody').html(obj[0]);
							pwcount=obj[1];
						}
					}).done(function() {
						tSawBclear(plocate);
					});

					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){
					$('[name="id"]').val(data.id);
					$('span.dpo').html(data.dpo);
					$('span.ddue').html(data.ddue);
					$('span.po').html(data.po);
					$('span.spk').html(data.spk);
					$('span.worth').html($.number(data.worth,0,',','.'));
					$('span.supplier_name').html(data.supplier_name);
					$('span.supplier_address').html(data.supplier_address.replace(/(?:\r\n|\r|\n)/g, '<br>'));
					$('span.purchasing_pic').html(data.purchasing_pic);
					$('span.term_payment').html(data.term);
					$('span.currency').html(data.currency);
					$('span.status').html(data.status);
					$('span.description').html(data.description);
					$('span.notes').html(data.notes.replace(/(?:\r\n|\r|\n)/g, '<br>'));
					$.ajax({type: 'post',url: modloc+'genpoitemrow',data: { id:data.id,view:true },success: function (response) {
							var obj = JSON.parse(response);
							$(dlocate+' tbody').html(obj[0]);
						}
					});
					$.ajax({type: 'post',url: modloc+'genattcrow',data: { id:data.id,view:true },success: function (response) {
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
	$('select#id_status').val('1').trigger('change.select2');
	wcount = 0;
	pwcount = 0;
	$(locate+' tbody').html('');
	$(plocate+' tbody').html('');
});

$('#frmInputData').on('change', 'select#id_supplier', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getsupplierinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="supplier_name"]').val(obj.supplier_name);
			$('[name="supplier_address"]').val(obj.supplier_address);
		}
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

// BOF operation
$("#addrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genpoitemrow',data: { count:wcount },success: function (response) {
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

$(locate).on("click", ".ibtnDel", function (event) {
	$(this).closest("tr").remove();
	updatetotal();
});
// EOF operation

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
// EOF calculate

// add attachment row
$("#addattcrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genattcrow',data: { count:pwcount },success: function (response) {
			newRow.append(response);
			$(plocate).append(newRow);
			//$(".id_attc").chosen({allow_single_deselect: true});
			pwcount++;
		}
	}).done(function() {
		tSawBclear(plocate);
	});
});

$(plocate).on("click", ".pibtnDel", function (event) {
	var row = $(this).attr('data-id');
	var file = $('a.attcfile_'+row).text()
	if(file){
		var id = $('[name="id"]').val();
		$.ajax({type: 'post',url: modloc+'rmattc',data: { id:id,file:file },success: function (response) {}});
	}
	$(this).closest("tr").remove();
});
<?php } ?>
</script>