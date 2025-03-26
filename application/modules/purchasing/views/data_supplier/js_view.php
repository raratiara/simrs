<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var idx; //for save index string
var ldx; //for save list index string

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
			code: {
				required: true
			},
			name: {
				required: true
			},
			contact_name: {
				required: true
			},
			contact_phone: {
				required: true
			},
			contact_email: {
				required: true
			},
			pic_name: {
				required: true
			},
			pic_phone: {
				required: true
			},
			pic_email: {
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
					$('[name="code"]').val(data.code);
					$('[name="name"]').val(data.name);
					$('[name="address"]').val(data.address);
					if(data.contact){
						var contact = JSON.parse(data.contact);
						$('[name="contact_name"]').val(contact.name);
						$('[name="contact_nik"]').val(contact.nik);
						$('[name="contact_address"]').val(contact.address);
						$('[name="contact_phone"]').val(contact.phone);
						$('[name="contact_email"]').val(contact.email);
						$('span.contact_filenik').html(setLink(data.id,contact.filenik));
					} else {
						$('span.contact_filenik').html('');
					}
					if(data.pic){
						var pic = JSON.parse(data.pic);
						$('[name="pic_name"]').val(pic.name);
						$('[name="pic_nik"]').val(pic.nik);
						$('[name="pic_address"]').val(pic.address);
						$('[name="pic_phone"]').val(pic.phone);
						$('[name="pic_email"]').val(pic.email);
						$('span.pic_filenik').html(setLink(data.id,pic.filenik));
					} else {
						$('span.pic_filenik').html('');
					}
					$('[name="nib"]').val(data.nib);
					$('[name="industry[]"]').val(JSON.parse(data.industry)).trigger('change.select2');
					$('[name="id_bank"]').val(data.id_bank).trigger('change.select2');
					$('[name="rek"]').val(data.rek);
					$('[name="npwp"]').val(data.npwp);
					if(data.document){
						var document = JSON.parse(data.document);
						$.each(document, function(i, val) {
							$('span.'+ i).html(setLink(data.id,val));
						});
					}
					$('[name="id_status"]').val(data.id_status).trigger('change.select2');
					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){
					$('span.code').html(data.code);
					$('span.name').html(data.name);
					$('span.address').html(data.address);
					$('span.contact_name').html('');
					$('span.contact_nik').html('');
					$('span.contact_filenik').html('');
					$('span.contact_address').html('');
					$('span.contact_phone').html('');
					$('span.contact_email').html('');
					if(data.contact){
						var contact = JSON.parse(data.contact);
						$('span.contact_name').html(contact.name);
						$('span.contact_nik').html(contact.nik);
						$('span.contact_filenik').html(setLink(data.id,contact.filenik));
						$('span.contact_address').html(contact.address);
						$('span.contact_phone').html(contact.phone);
						$('span.contact_email').html(contact.email);
					}
					$('span.pic_name').html('');
					$('span.pic_nik').html('');
					$('span.pic_filenik').html('');
					$('span.pic_address').html('');
					$('span.pic_phone').html('');
					$('span.pic_email').html('');
					if(data.pic){
						var pic = JSON.parse(data.pic);
						$('span.pic_name').html(pic.name);
						$('span.pic_nik').html(pic.nik);
						$('span.pic_filenik').html(setLink(data.id,pic.filenik));
						$('span.pic_address').html(pic.address);
						$('span.pic_phone').html(pic.phone);
						$('span.pic_email').html(pic.email);
					}
					$('span.nib').html(data.nib);
					$('span.industry').html(data.usaha);
					$('span.bank').html(data.bank);
					$('span.rek').html(data.rek);
					$('span.npwp').html(data.npwp);
					if(data.document){
						var document = JSON.parse(data.document);
						$.each(document, function(i, val) {
							var gofile = '';
							$('span.'+ i).html(setLink(data.id,val));
						});
					}
					$('span.status').html(data.status);
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
	var obj = {"contact_filenik":"","pic_filenik":"","doc_1":"","doc_2":"","doc_3":"","doc_4":"","doc_5":"","doc_6":"","doc_7":"","doc_8":"","doc_9":"","doc_10":"","doc_11":"","doc_12":"","doc_13":""};
	$.each(obj, function(i, val) {
		$('span.'+ i).html(val);
	});
});

function setLink(i,f){
	var link = '';
	if(f){
		link = '<a href="/uploads/data/supplier/'+i+'/'+f+'" target="_blank">'+f+'</a>';
	}
	
	return link;
}
<?php } ?>
</script>