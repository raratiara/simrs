<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modquoloc = '/sales/quotation/';
var modloc = '/sales/po_in/';
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
				id_customer: {
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
					$('[name="worth"]').number(true,0,',','.');
					$('[name="worth"]').val(data.worth);
					$('select#id_quotation').val(data.id_quotation).trigger('change.select2');
					$('select#id_project').val(data.id_project).trigger('change.select2');
					$('select#id_customer').val(data.id_customer).trigger('change.select2');
					$('[name="customer_name"]').val(data.customer_name);
					$('[name="customer_address"]').val(data.customer_address);
					$('select#id_pic').val(data.id_pic).trigger('change.select2');
					$('[name="id_term"][value="'+data.id_term+'"]').prop('checked', true);
					$('[name="id_currency"][value="'+data.id_currency+'"]').prop('checked', true);
					$('select#id_status').val(data.id_status).trigger('change.select2');
					if(data.tax){
						var tax = JSON.parse(data.tax);
						$('[name="is_tax[]"]').val(tax.is_tax).trigger('change.select2');
						$('[name="is_tax_other"]').val(tax.tax_other);
					}
					if(data.top){
						var top = JSON.parse(data.top);
						$('[name="top_dp"]').val(top.top_dp);
						$('[name="top_t1"]').val(top.top_t1);
						$('[name="top_t2"]').val(top.top_t2);
						$('[name="top_t3"]').val(top.top_t3);
						$('[name="top_tf"]').val(top.top_tf);
						$('[name="top_rt"]').val(top.top_rt);
					}
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
					$('span.worth').html($.number(data.worth,0,',','.'));
					$('span.quotation').html(data.quotation);
					$('span.project').html(data.project);
					$('span.customer_name').html(data.customer_name);
					$('span.customer_address').html(data.customer_address.replace(/(?:\r\n|\r|\n)/g, '<br>'));
					$('span.sales_pic').html(data.sales_pic);
					$('span.term_payment').html(data.term);
					$('span.currency').html(data.currency);
					$('span.status').html(data.status);
					if(data.tax){
						var tax = JSON.parse(data.tax);
						$('span.is_tax').html(tax.is_tax.join());
						$('span.is_tax_other').html(tax.tax_other);
						if(tax.tax_other){
							$('span.is_tax_other').html(' : '+tax.tax_other);
						}
					}
					if(data.top){
						var top = JSON.parse(data.top);
						$('span.top_dp').html(top.top_dp);
						$('span.top_t1').html(top.top_t1);
						$('span.top_t2').html(top.top_t2);
						$('span.top_t3').html(top.top_t3);
						$('span.top_tf').html(top.top_tf);
						$('span.top_rt').html(top.top_rt);
					}
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

/* Quote load */
$('#frmInputData').on('change', 'select#id_quotation', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getquoteinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="worth"]').number(true,0,',','.');
			$('[name="worth"]').val(obj.total_quote);
			$('select#id_project').val(obj.id_project).trigger('change.select2');
			$('select#id_customer').val(obj.id_customer).trigger('change.select2');
			$('[name="customer_name"]').val(obj.customer_name);
			$('[name="customer_address"]').val(obj.customer_address);
			$('[name="id_term"][value="'+obj.id_term+'"]').prop('checked', true);
			$('[name="id_currency"][value="'+obj.id_currency+'"]').prop('checked', true);
			if(obj.tax){
				var tax = JSON.parse(obj.tax);
				$('[name="is_tax[]"]').val(tax.is_tax).trigger('change.select2');
				$('[name="is_tax_other"]').val(tax.tax_other);
			}
			if(obj.top){
				var top = JSON.parse(obj.top);
				$('[name="top_dp"]').val(top.top_dp);
				$('[name="top_t1"]').val(top.top_t1);
				$('[name="top_t2"]').val(top.top_t2);
				$('[name="top_t3"]').val(top.top_t3);
				$('[name="top_tf"]').val(top.top_tf);
				$('[name="top_rt"]').val(top.top_rt);
			}
			$('[name="description"]').val(obj.description);
			$('[name="notes"]').val(obj.notes);
			$.ajax({type: 'post',url: modloc+'genquoteitemrow',data: { id:obj.id },success: function (response) {
					var objc = JSON.parse(response);
					$(locate+' tbody').html(objc[0]);
					$(locate+' input.qty').number(true,0,',','.');
					$(locate+' input.price').number(true,0,',','.');
					$(locate+' input.subtotal').number(true,2,',','.');
					wcount=objc[1];
				}
			}).done(function() {
				//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
				tSawBclear(locate);
			});
			$('select#id_status').val('1').trigger('change.select2');
			$.uniform.update();
		}
	});
});

$('#frmInputData').on('change', 'select#id_customer', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getcustomerinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_quotation').val('').trigger('change.select2');
			$('select#id_project').val('').trigger('change.select2');
			$('[name="customer_name"]').val(obj.customer_name);
			$('[name="customer_address"]').val(obj.customer_address);
		}
	});
});

$('#frmInputData').on('change', 'select#id_project', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getprojectinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_customer').val(obj.id_customer).trigger('change.select2');
			$('[name="customer_name"]').val(obj.customer_name);
			$('[name="customer_address"]').val(obj.customer_address);
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