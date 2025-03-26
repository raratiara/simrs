<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/sales/data_pca/';
var opsForm = 'form#frmInputData';
var locate = 'table.rab-list';
var dlocate = 'table.drab-list';
var wcount = 0; //for member list row identify
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
		  { "sClass": "text-center", "aTargets": [ 0,1 ] },
		  { "sClass": "dt-body-right", "aTargets": [ 5 ] }
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
				worth: {
					number: true
				},
				budget: {
					number: true
				},
				margin: {
					number: true
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
					$('select#id_project').val(data.id_project).trigger('change.select2');
					$('[name="title"]').val(data.title);
					$('[name="total_worth"]').number(true,0,',','.');
					$('[name="total_worth"]').val(data.nilai_proyek);
					$('[name="total_budget"]').number(true,0,',','.');
					$('[name="total_budget"]').val(data.total_budget);
					var margin = data.nilai_proyek - data.total_budget;
					$('[name="margin"]').number(true,0,',','.');
					$('[name="margin"]').val(margin);
					$.ajax({type: 'post',url: modloc+'genrabrow',data: { id:data.id },success: function (response) {
							var obj = JSON.parse(response);
							$(locate+' tbody').html(obj[0]);
							$(locate+' input.budget').number(true,0,',','.');
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
					$('span.project').html(data.project);
					$('span.project_title').html(data.title);
					$('span.total_worth').html($.number(data.nilai_proyek,0,',','.'));
					$('span.total_budget').html($.number(data.total_budget,0,',','.'));
					var margin = data.nilai_proyek - data.total_budget;
					$('span.margin').html($.number(margin,0,',','.'));
					$.ajax({type: 'post',url: modloc+'genrabrow',data: { id:data.id,view:true },success: function (response) {
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
	$('[name="total_worth"]').number(true,0,',','.');
	$('[name="total_budget"]').number(true,0,',','.');
	$('[name="margin"]').number(true,0,',','.');
	wcount = 0;
	pwcount = 0;
	$(locate+' tbody').html('');
	$(plocate+' tbody').html('');
});

/* po change */
$(opsForm).on('change', 'select#id_project', function(event){
	var id = $(this).val();
	if(id){
		$.ajax({type: 'post',url: modloc+'getprojectinfo',data: { sel:id },success: function (response) {
				var obj = JSON.parse(response);
				$('[name="title"]').val(obj.title);
				$('[name="total_worth"]').number(true,0,',','.');
				$('[name="total_worth"]').val(obj.nilai_proyek);
				$('[name="total_budget"]').number(true,0,',','.');
				$('[name="total_budget"]').val(0);
				$('[name="margin"]').number(true,0,',','.');
				$('[name="margin"]').val(0);
				updatemargin();
			}
		});
	}
});

/* wbs change */
$(locate).on('change', 'select.id_wbs', function(event){
	var id = $(this).val();
	var row = $(this).attr("meta:index");
	$(locate+' [name="description['+row+']"]').val('');
	$(locate+' [name="budget['+row+']"]').val(0);
	updatemargin();
});

$(locate).on('keyup', 'input.budget', function(event){
	var row = $(this).attr('data-id');
	$(this).number(true,0,',','.');
	updatemargin();
});

function updatemargin()
{
	expire();
	var wpo = goFloat($(opsForm+' [name="total_worth"]').val());
	var srow = $(locate+' input.budget').length;
	var wbudget = 0;
	var wmargin = 0;
	for(var i = 0; i < srow; i++) {
		wbudget += goFloat($(locate+' input.budget').eq(i).val());
	}
	wmargin = wpo - wbudget;
	$(opsForm+' [name="total_budget"]').val(wbudget);
	$(opsForm+' [name="margin"]').val(wmargin);
}

// BOF table list operation
// add rab row
$("#addrabrow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: modloc+'genrabrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			//$(".id_rab").chosen({allow_single_deselect: true});
			wcount++;
			$(locate+' [name="budget['+wcount+']"]').number(true,0,',','.');
		}
	}).done(function() {
		tSawBclear(locate);
	});
});

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

$(locate).on("click", ".ibtnDel", function (event) {
	$(this).closest("tr").remove();
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
// EOF table list operation
<?php } ?>
</script>