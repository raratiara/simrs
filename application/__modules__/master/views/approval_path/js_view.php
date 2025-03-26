<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/master/approval_path/';
var opsForm = 'form#frmInputData';
var locate = 'table.member-list';
var dlocate = 'table.dmember-list';
var wcount = 0; //for member list row identify

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
		  { "bSortable": false, "aTargets": [ 0, 1 ] },
		  { "sClass": "text-center", "aTargets": [ 0,1 ] }
		],
		"aaSorting": [
		  	[1,'asc'] 
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
				description: {
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
					$('select#id_menu').val(data.id_menu).trigger('change.select2');
					setapprsel(data.id_menu,data.id_approval);
					$('[name="active"][value="'+data.active+'"]').prop('checked', true);
					$.ajax({type: 'post',url: modloc+'genmemberrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_member").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
					});
					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){
					$('[name="id"]').val(data.id);
					$('span.menu').html(data.menu);
					$('span.approval').html(data.approval);
					var active = 'No';
					if(data.active == 1){
						active = 'Yes';
					}
					$('span.active').html(active);
					$.ajax({type: 'post',url: modloc+'genmemberrow',data: { id:data.id,view:true },success: function (response) {
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
	$(locate+' tbody').html('');
	wcount = 0;
});

/* member change */
$(locate).on('change', 'select.id_member', function(event){
	var id= $(this).val();
	var row = $(this).attr("meta:index");
	$('span.jabatan[data-id="'+row+'"]').html('');
	$('span.telp[data-id="'+row+'"]').html('');
	$('span.email[data-id="'+row+'"]').html('');
	if(id){
		$.ajax({type: 'post',url: modloc+'getmemberinfo',data: { sel:id },success: function (response) {
				var obj = JSON.parse(response);
				$('span.jabatan[data-id="'+row+'"]').html(obj['jabatan']);
				$('span.telp[data-id="'+row+'"]').html(obj['phone']);
				$('span.email[data-id="'+row+'"]').html(obj['email']);
			}
		});
	}
});

$(opsForm).on('change', 'select#id_menu', function(event){
	var smn= $(this).val();
	setapprsel(smn,'');
});

function setapprsel(mn,id){
	var sid = id;
	$.ajax({type: 'post',url: modloc+'getapprsel',data: { smn:mn,sel:id },success: function (response) {
			$('select#id_approval').html(response);
			if(sid){
				$('select#id_approval').val(sid).trigger('change.select2');
			} else {
				$('select#id_approval').val('').trigger('change.select2');
			}
		}
	});
}

// BOF table list operation
// add member row
$("#addmemberrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genmemberrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			//$(".id_member").chosen({allow_single_deselect: true});
			wcount++;
		}
	}).done(function() {
		tSawBclear(locate);
	});
});

$(locate).on("click", ".ibtnDel", function (event) {
	$(this).closest("tr").remove();
});
// EOF table list operation
<?php } ?>
</script>