
/* reload table list */
function reload_table()
{
	expire();
    myTable.ajax.reload(); //reload datatable ajax 
}

/* checking session */
function expire()
{
    $.post( "<?=_URL;?>login/hassession", { id: "check" }, function( data ) {
		if(data=='false') location.reload();
		//if(!data) location.reload();
	});
}


<?php if  (_USER_ACCESS_LEVEL_ADD == "1") { ?>
/* open add form modal */
$( "#btnAddData" ).on('click', function(){
	expire();
	save_method = 'add';
	reset();
	$('#mfdata').text('Add');
	$('#modal-form-data').modal('show');
});
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_UPDATE == "1") { ?>
/* open edit form modal */
function edit(id)
{
	expire();
    save_method = 'update';
	idx = id;
	reset();
}
<?php } ?>


<?php if  (_USER_ACCESS_LEVEL_DETAIL == "1") { ?>
/* open detail modal */
function detail(id)
{
	expire();
    save_method = 'detail';
	idx = id;
	load_data();
}
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1") { ?>
$('#frmInputData select').on('change', function (e) {
    $("#frmInputData").valid();
});

/* reset form data */
function reset(){
	validator.resetForm();
	$('#frmInputData')[0].reset();
	$(".select2me").select2({dropdownParent:$('#modal-form-data'),placeholder:"Select",width:"auto",allowClear:!0});
	$('#frmInputData select').val('').trigger('change.select2');
	$.uniform.update();
	if(save_method == 'update') {
		load_data();
	}
}
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
/* do delete command */
function deleting(id)
{
	expire();
    save_method = 'delete';
	$('span#ids').html(id);
	$('#frmDeleteData [name="id"]').val(id);
	$('#modal-delete-data').modal('show');
	//save();
}	

/* do bulk delete command */
$( "#btnBulkData" ).on('click', function(){
	expire();
    save_method = 'bulk';
	ldx = [];
    $(".data-check:checked").each(function() {
            ldx.push(this.value);
    });
	
    if(ldx.length > 0)
    {
		var ids = ldx.join(", ")
		$('span#ids').html(ids);
		$('#modal-delete-bulk-data').modal('show');
	} else {
		title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-exclamation-circle fa-5x" style="color:red"></i></div>';
		var dialog = bootbox.dialog({
			message: title+'<center>No data selected</center>'
		});
		setTimeout(function(){
			dialog.modal('hide');
		}, 1500);
	}
});
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_EKSPORT == "1") { ?>
/* open export modal */
$( "#btnEksportData" ).on('click', function(){
	expire();
	save_method = 'export';
	$('#modal-eksport-data').modal('show');
});
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_IMPORT == "1") { ?>
/* open import modal */
$( "#btnImportData" ).on('click', function(){
	expire();
	save_method = 'import';
	$('#frmImportData')[0].reset();
	$(".progress-bar").width('0%');
	$(".progress-bar").html('0%');
	$('#modal-import-data').modal('show');
});
<?php } ?>

/* processing action */
function save()
{
	expire();
    var title;
    var send_url;
    var form_check;
    var post_type = 'POST';
    var smsg;
	var formData;

<?php if  (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1") { ?>
	if(save_method == 'add' || save_method == 'update') {
		form_check = $("#frmInputData").valid();
		if(!form_check) return false;
	}
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_ADD == "1") { ?>
	if(save_method == 'add') {
		send_url = module_path+'/add';
		formData = new FormData($('#frmInputData')[0]);
	}
<?php } ?>
	
<?php if  (_USER_ACCESS_LEVEL_UPDATE == "1") { ?>
	if(save_method == 'update'){
		send_url = module_path+'/edit';
		formData = new FormData($('#frmInputData')[0]);
	} 
<?php } ?>
	
<?php if  (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
	if(save_method == 'delete'){
		send_url = module_path+'/delete';
		formData = new FormData($('#frmDeleteData')[0]);
	}

	if(save_method == 'bulk'){
		send_url = module_path+'/bulk';
		formData = new FormData($('#frmListData')[0]);
	}
<?php } ?>
	
<?php if  (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1" || _USER_ACCESS_LEVEL_DELETE == "1") { ?>
	if((save_method == 'add') || (save_method == 'update') || (save_method == 'delete') || (save_method == 'bulk')) {
		$.ajax({
			type: post_type,
			url: send_url,
			data: formData,
			contentType: false,
			processData: false,
			cache: false,
			dataType: "JSON",
			success: function( response ) {
				if(response.status){
					title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-check-circle-o fa-5x" style="color:green"></i></div>';
					btn = '';
					if(save_method == 'add' || save_method == 'update') {
						$('#frmInputData')[0].reset();
						$('#modal-form-data').modal('hide');
					} else if(save_method == 'delete'){
						$('#modal-delete-data').modal('hide');
					} else if(save_method == 'bulk'){
						$('#modal-delete-bulk-data').modal('hide');
					}
					if(pout){
						printit(response.pid);
					}
					pout = false;
					reload_table();
				} else {
					title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-exclamation-circle fa-5x" style="color:red"></i></div>';
					btn = '<br/><button class="btn blue" data-dismiss="modal">OK</button>';
				}
				var dialog = bootbox.dialog({
					message: title+'<center>'+response.msg+btn+'</center>'
				});
				if(response.status){
					setTimeout(function(){
						dialog.modal('hide');
					}, 1500);
				}

			},
			error: function (jqXHR, textStatus, errorThrown) {
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

<?php if  (_USER_ACCESS_LEVEL_EKSPORT == "1") { ?>
	if(save_method == 'export'){
		send_url = module_path+'/eksport';
		formData = $('#frmEksportData').serialize();
		window.location = send_url+'?'+formData;
		$('#modal-eksport-data').modal('hide');
	}
<?php } ?>

<?php if  (_USER_ACCESS_LEVEL_IMPORT == "1") { ?>
	if(save_method == 'import'){
		send_url = module_path+'/import';
		formData = new FormData($('#frmImportData')[0]);
		$.ajax({
            xhr: function() {
                var xhr = new window.XMLHttpRequest();
                xhr.upload.addEventListener("progress", function(evt) {
                    if (evt.lengthComputable) {
                        var percentComplete = ((evt.loaded / evt.total) * 100);
                        $(".progress-bar").width(percentComplete + '%');
                        $(".progress-bar").html(percentComplete+'%');
                    }
                }, false);
                return xhr;
            },
			type: post_type,
			url: send_url,
			data: formData,
			contentType: false,
			processData: false,
			cache: false,
			dataType: "JSON",
            beforeSend: function(){
                $(".progress-bar").width('0%');
            },
			success: function( response ) {
				if(response.status){
					title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-check-circle-o fa-5x" style="color:green"></i></div>';
					btn = '';
					$('#frmImportData')[0].reset();
					$('#modal-import-data').modal('hide');
					reload_table();
				} else {
					$('#frmImportData')[0].reset();
					$(".progress-bar").width('0%');
					$(".progress-bar").html('0%');
					title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-exclamation-circle fa-5x" style="color:red"></i></div>';
					btn = '<br/><button class="btn blue" data-dismiss="modal">OK</button>';
				}
				var dialog = bootbox.dialog({
					message: title+'<center>'+response.msg+btn+'</center>'
				});
				if(response.status){
					setTimeout(function(){
						dialog.modal('hide');
					}, 1500);
				}
			},
			error: function (jqXHR, textStatus, errorThrown) {
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
}

function formatNum(num,fx=2,dec=',',thou='.') {
    var p = parseFloat(num).toFixed(fx).split(".");
    return p[0].split("").reverse().reduce(function(acc, num, i, orig) {
        return  num=="-" ? acc : num + (i && !(i % 3) ? thou : "") + acc;
    }, "") + dec + p[1];
}

function goFloat(num){
	if(typeof num == 'string'){
		if(checkStrNumFormat(num,2,',')){
			num = parseFloat(num.replace(/[^\d,-]/g,'').replace(',','.'));
		} else {
			num = parseFloat(num);
		}
	}
	
	return num
}

function checkStrNumFormat(str,pos,chk) {
  var res = str.charAt(str.length-(pos+1));
  if(res == chk){
	  return true;
  } else {
	  return false;
  }
}

function ucwords (str) {
    return (str + '').replace(/^([a-z])|\s+([a-z])/g, function ($1) {
        return $1.toUpperCase();
    });
}

function tSawBclear(elem){
	Tablesaw.init(elem);
	var ts = $(elem);
	$(document).off("." + ts.attr("id"));
	$(window).off("." + ts.attr("id"));
	ts.removeData('tablesaw');
}

function saveprint()
{
	pout = true;
	save();
}

function goprint()
{
	var pid = $('#frmDetailData input[name="id"]').val();
	printit(pid);
}

function printit(id)
{
	expire();
    $.post( module_path+'/print', { pid: id }, function( data ) {
		printWindow = window.open('');
		printWindow.document.write(data);
		printWindow.print();
	});
}	
