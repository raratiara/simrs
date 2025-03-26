<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var idx; //for save index string
var ldx; //for save list index string

var modloc = '/_13_simrs/setup/dokter_menu/';
var opsForm = 'form#frmInputData';
var locate = 'table.reim-list';
var dlocate = 'table.dreim-list';
var wcount = 0; //for reimburse list row identify


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

	$( "#btnAddData" ).on('click', function(){ 
		$('[name="as_draft"]').val('0');
		$('select#last_status').val('1').trigger('change.select2');
		/*setinitform();
		setact('','');*/
		wcount = 0;
		pwcount = 0;
		$(locate+' tbody').html('');
	});

	validator = $("#frmInputData").validate({
		errorElement: 'span', //default input error message container
		errorClass: 'help-block help-block-error', // default input error message class
		focusInvalid: false, // do not focus the last invalid input
		ignore: "", // validate all fields including form hidden input
		rules: {
			title: {
				required: true
			},
			module_name: {
				required: true
			},
			url: {
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
					$('[name="spesialis"]').val(data.spesialis); 
					$('[name="nama"]').val(data.nama);
					
					$('span.file_foto').html('<img src="http://localhost/_13_simrs/uploads/dokter/'+data.foto+'" width="300" height="300" >');
					$('[name="tgljoin"]').val(data.date_join);
					$('select#poli').val(data.poli_id).trigger('change.select2');


					//var lstatus = 0; //data.last_status;
					$.ajax({type: 'post',url: modloc+'genexpensesrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
						///expenseviewadjust(lstatus);
					});
					///formviewadjust(data.last_status);
					///setact(data.id);


					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){ 
					$('span.poli').html(data.poli);
					$('span.spesialis').html(data.spesialis);
					$('span.nama').html(data.nama);
					$('span.foto').html('<img src="http://localhost/_13_simrs/uploads/dokter/'+data.foto+'" width="300" height="300" >');
					$('span.tgljoin').html(data.date_join);


					$.ajax({type: 'post',url: modloc+'genexpensesrow',data: { id:data.id, view:true },success: function (response) {
							var obj = JSON.parse(response);
							$(dlocate+' tbody').html(obj[0]);
							
							wcount=obj[1];
						}
					}).done(function() {
						//$(".id_wbs").chosen({width: "100%",allow_single_deselect: true});
						tSawBclear(locate);
						///expenseviewadjust(lstatus);
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

$("#addreimrow").on("click", function () { 
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: module_path+'/genexpensesrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			wcount++;
			
		}
	}).done(function() {
		tSawBclear('table.order-list');
		/*$('.date-picker').datepicker({
			rtl: App.isRTL(),
			autoclose: true,
			clearBtn: true,
			todayHighlight: true
		});*/
	});
});

function del(idx,hdnid){
	
	if(hdnid != ''){ //delete dr table

		$.ajax({type: 'post',url: module_path+'/delrowJadwal',data: { id:hdnid },success: function (response) {
				
			}
		}).done(function() {
			tSawBclear('table.order-list');
		});

	}

	//delete tampilan row

	var table = document.getElementById("tblJadwal");
	table.deleteRow(idx);
	

}



</script>