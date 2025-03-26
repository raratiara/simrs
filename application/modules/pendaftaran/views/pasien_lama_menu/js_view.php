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
					$('[name="no_rm"]').val(data.no_rekam_medis);
					$('[name="tgl_lahir"]').val(data.tgl_lahir);
					$('[name="nama_pasien"]').val(data.pasien);
					$('[name="alamat"]').val(data.alamat_tinggal);
					$('[name="alamat_no"]').val(data.no_tinggal);
					$('[name="alamat_rt"]').val(data.rt_tinggal);
					$('[name="alamat_rw"]').val(data.rw_tinggal);
					$('[name="prov"]').val(data.prov);
					$('[name="kec"]').val(data.kec);
					$('[name="kota"]').val(data.kota);
					$('[name="kel"]').val(data.kel);
					$('[name="jenis_pembayaran"][value="'+data.jenis_pembayaran+'"]').prop('checked', true);
					$('select#poli').val(data.poli_id).trigger('change.select2');
					$('select#dokter').val(data.dokter_id).trigger('change.select2');
					
					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){ 
					$('[name="id"]').val(data.id);
					$('span.tgl_pendaftaran').html(data.date_pendaftaran);
					$('span.tgl_jadwal_pemeriksaan').html(data.date_jadwal_pemeriksaan);
					$('span.jam_jadwal_pemeriksaan').html(data.jam_jadwal_pemeriksaan);
					$('span.no_urut').html(data.no_urut);
					$('span.poli').html(data.poli);
					$('span.dokter').html(data.dokter);
					$('span.jenis_pembayaran').html(data.jenis_pembayaran);
					$('span.status').html(data.status_name);
					$('span.pasien').html(data.pasien);
					$('span.alamat').html(data.alamat_tinggal);
					$('span.alamat_no').html(data.no_tinggal);
					$('span.alamat_rt').html(data.rt_tinggal);
					$('span.alamat_rw').html(data.rw_tinggal);
					$('span.prov').html(data.prov);
					$('span.kec').html(data.kec);
					$('span.kab').html(data.kota);
					$('span.kel').html(data.kel);
					
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


function getDataPasien() { 
 	var val = document.getElementById("no_rm").value;
  	
  	setform(val);
}

function setform(val){ 
	$.ajax({
		type: "POST",
        url : module_path+'/get_data_by_norm',
		data: { 'no_rm': val },
		cache: false,		
        dataType: "JSON",
        success: function(data)
        { console.log(data);
			if(data != false){ 
				$('[name="pasien_id"]').val(data[0]['pasien_id']);
				$('[name="nama_pasien"]').val(data[0]['pasien']);
				$('[name="alamat"]').val(data[0]['alamat_tinggal']);
				$('[name="alamat_no"]').val(data[0]['no_tinggal']);
				$('[name="alamat_rt"]').val(data[0]['rt_tinggal']);
				$('[name="alamat_rw"]').val(data[0]['rw_tinggal']);
				$('[name="prov"]').val(data[0]['prov']);
				$('[name="kec"]').val(data[0]['kec']);
				$('[name="kab"]').val(data[0]['kab']);
				$('[name="kel"]').val(data[0]['kel']);



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

</script>