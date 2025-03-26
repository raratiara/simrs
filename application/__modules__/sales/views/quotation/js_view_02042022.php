<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/sales/quotation/';
var opsForm = 'form#frmInputData';
var locate = 'table.order-list';
var dlocate = 'table.dorder-list';
var wcount = 0; //for item list row identify

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
				date_quotation: {
					required: true
				},
				quotation: {
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
				id_valid: {
					required: true
				},
				id_ttd: {
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
					$('[name="date_quotation"]').val(data.dquotation);
					$('[name="quotation"]').val(data.quotation);
					$('[name="rab"]').number(true,0,',','.');
					$('[name="rab"]').val(data.rab);
					$('[name="total_quote"]').number(true,0,',','.');
					$('[name="total_quote"]').val(data.total_quote);
					$('[name="grandtotal"]').val(data.grandtotal);
					$('[name="ppn"]').val(data.ppn);
					$('[name="pph"]').val(data.pph);
					$('[name="other_tax"]').val(data.other_tax);
					$('[name="margin_plan"]').number(true,0,',','.');
					$('[name="margin_plan"]').val(data.margin_plan);
					$('select#id_rab').val(data.id_rab).trigger('change.select2');
					$('select#id_customer').val(data.id_customer).trigger('change.select2');
					$('[name="customer_name"]').val(data.customer_name);
					$('[name="customer_address"]').val(data.customer_address);
					$('[name="id_term"][value="'+data.id_term+'"]').prop('checked', true);
					$('[name="id_currency"][value="'+data.id_currency+'"]').prop('checked', true);
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
					$('[name="id_valid"][value="'+data.id_valid+'"]').prop('checked', true);
					$('[name="to_contact"]').val(data.to_contact);
					$('select#id_ttd').val(data.id_ttd).trigger('change.select2');
					$.ajax({type: 'post',url: modloc+'genquoteitemrow',data: { id:data.id },success: function (response) {
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
					$('[name="id"]').val(data.id);
					$('span.project').html(data.project);
					$('span.customer_name').html(data.customer_name);
					$('span.customer_address').html(data.customer_address.replace(/(?:\r\n|\r|\n)/g, '<br>'));
					$('span.dquotation').html(data.dquotation);
					$('span.quotation').html(data.quotation);
					$('span.rab').html($.number(data.rab,0,',','.'));
					$('span.total_quote').html($.number(data.total_quote,0,',','.'));
					$('span.margin_plan').html($.number(data.margin_plan,0,',','.'));
					$('span.term_payment').html(data.term);
					$('span.currency').html(data.currency);
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
					$('span.term_valid').html(data.valid);
					$('span.to_contact').html(data.to_contact);
					$('span.ttd').html(data.ttd);
					$.ajax({type: 'post',url: modloc+'genquoteitemrow',data: { id:data.id,view:true },success: function (response) {
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
$( "#btnAddData" ).on('click', function(){
	wcount = 0;
	$(locate+' tbody').html('');
});

/* RAB load */
$('#frmInputData').on('change', 'select#id_rab', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getrabinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('select#id_customer').val(obj.id_customer).trigger('change.select2');
			$('[name="customer_name"]').val(obj.customer_name);
			$('[name="customer_address"]').val(obj.customer_address);
			$('[name="rab"]').number(true,0,',','.');
			$('[name="rab"]').val(obj.total_budget);
			updatetotal();
		}
	});
});

$('#frmInputData').on('change', 'select#id_customer', function(event){
	var id= $(this).val();
	$.ajax({type: 'post',url: module_path+'/getcustomerinfo',data: { sel:id },success: function (response) {
			var obj = JSON.parse(response);
			$('[name="customer_name"]').val(obj.customer_name);
			$('[name="customer_address"]').val(obj.customer_address);
			$('select#id_rab').val('').trigger('change.select2');
			$('[name="rab"]').number(true,0,',','.');
			$('[name="rab"]').val(0);
			updatetotal();
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
	$.ajax({type: 'post',url: modloc+'genquoteitemrow',data: { count:wcount },success: function (response) {
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
	var oRab = goFloat($(opsForm+' [name="rab"]').val());
	var srow = $(locate+' input.subtotal').length;
	var oGross = 0;
	var oGrand = 0;
	var oPpn = 0;
	var oPph = 0;
	var oOther = 0;
	var oMargin = 0;
	for(var i = 0; i < srow; i++) {
		oGross += goFloat($(locate+' input.subtotal').eq(i).val());
	}
	$(".is_tax:checked").each(function () {
		var check = $(this).val();
		if(check=='ppn'){
			oPpn = oGross*0.1;
		} else if(check=='pph'){
		} else if(check=='other'){
		}
	});
	oGrand = oGross+oPpn+oPph+oOther;
	oMargin = oRab-oGross;
	$(opsForm+' [name="total_quote"]').number(true,0,',','.');
	$(opsForm+' [name="total_quote"]').val(oGross);
	$(opsForm+' [name="margin_plan"]').number(true,0,',','.');
	$(opsForm+' [name="margin_plan"]').val(oMargin);
	$(opsForm+' [name="grandtotal"]').val(oGrand);
	$(opsForm+' [name="ppn"]').val(oPpn);
	$(opsForm+' [name="pph"]').val(oPph);
	$(opsForm+' [name="other_tax"]').val(oOther);
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
<?php } ?>
</script>