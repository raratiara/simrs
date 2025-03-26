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
					$('[name="nama_pasien"]').val(data.nama);
					$('[name="tempat_lahir"]').val(data.tempat_lahir);
					$('[name="tgl_lahir"]').val(data.tgl_lahir);
					$('[name="pekerjaan"]').val(data.pekerjaan);
					$('[name="no_hp"]').val(data.no_hp);
					$('[name="email"]').val(data.email);
					$('[name="alamat_tinggal"]').val(data.alamat_tinggal);
					$('[name="alamat_tinggal_no"]').val(data.no_tinggal);
					$('[name="alamat_tinggal_rt"]').val(data.rt_tinggal);
					$('[name="alamat_tinggal_rw"]').val(data.rw_tinggal);
					$('[name="no_bpjs"]').val(data.no_bpjs);
					$('[name="no_identitas"]').val(data.no_identitias);
					$('[name="alamat_identitas"]').val(data.alamat_identitas);
					$('[name="alamat_identitas_no"]').val(data.no_identitas);
					$('[name="alamat_identitas_rt"]').val(data.rt_identitas);
					$('[name="alamat_identitas_rw"]').val(data.rw_identitas);
					$('[name="nama_ayah"]').val(data.nama_lengkap_ayah);
					$('[name="nama_pasangan"]').val(data.nama_lengkap_pasangan);
					$('[name="nama_penanggung"]').val(data.nama_penanggung_jawab);
					$('[name="nama_ibu"]').val(data.nama_lengkap_ibu);
					$('[name="nohp_pasangan"]').val(data.hp_pasangan);
					$('[name="nohp_penanggung"]').val(data.hp_penanggung_jawab);
					$('[name="jenis_kelamin"][value="'+data.jenis_kelamin+'"]').prop('checked', true);

					$('select#tipe_identitas').val(data.jenis_identitas_id).trigger('change.select2');
					$('select#agama').val(data.agama_id).trigger('change.select2');
					$('select#pendidikan').val(data.pendidikan).trigger('change.select2');
					$('select#status_kawin').val(data.status_kawin).trigger('change.select2');
					$('select#prov_tempattinggal').val(data.provinsi_id_tinggal).trigger('change.select2');
					$('select#kec_tempattinggal').val(data.kec_id_tinggal).trigger('change.select2');
					$('select#kota_tempattinggal').val(data.kabkota_id_tinggal).trigger('change.select2');
					$('select#kel_tempattinggal').val(data.kel_id_tinggal).trigger('change.select2');
					$('select#prov_tempatidentitas').val(data.prov_id_identitas).trigger('change.select2');
					$('select#kec_tempatidentitas').val(data.kec_id_identitas).trigger('change.select2');
					$('select#kota_tempatidentitas').val(data.kabkota_id_identitas).trigger('change.select2');
					$('select#kel_tempatidentitas').val(data.kel_id_identitas).trigger('change.select2');
					$('span.file_foto_bpjs').html('<img src="http://localhost/_13_simrs/uploads/pasien/'+data.attachment_bpjs+'" width="200" height="200" >');
					$('span.file_foto_identitas').html('<img src="http://localhost/_13_simrs/uploads/pasien/'+data.attachment_identitas+'" width="200" height="200" >');

					$.uniform.update();
					$('#mfdata').text('Update');
					$('#modal-form-data').modal('show');
				}
				if(save_method == 'detail'){ 
					$('[name="id"]').val(data.id);
					$('span.no_rm').html(data.no_rekam_medis);
					$('span.nama_pasien').html(data.nama);
					$('span.ttl').html(data.tgl_lahir);
					$('span.jenis_kelamin').html(data.jenis_kelamin_desc);
					$('span.agama').html(data.agama);
					$('span.pekerjaan').html(data.pekerjaan);
					$('span.no_hp').html(data.no_hp);
					$('span.pendidikan').html(data.pendidikan_desc);
					$('span.status_kawin').html(data.status_kawin_desc);
					$('span.email').html(data.email);
					$('span.alamat_tinggal').html(data.alamat_tinggal);
					$('span.alamat_tinggal_no').html(data.no_tinggal);
					$('span.alamat_tinggal_rt').html(data.rt_tinggal);
					$('span.alamat_tinggal_rw').html(data.rw_tinggal);
					$('span.prov').html(data.provinsi_tinggal);
					$('span.kec').html(data.kec_tinggal);
					$('span.kab').html(data.kabkota_tinggal);
					$('span.kel').html(data.kel_tinggal);
					$('span.no_bpjs').html(data.no_bpjs);
					$('span.no_identitas').html(data.no_identitias);
					$('span.alamat_identitas').html(data.alamat_identitas);
					$('span.alamat_identitas_no').html(data.no_identitas);
					$('span.alamat_identitas_rt').html(data.rt_identitas);
					$('span.alamat_identitas_rw').html(data.rw_identitas);
					$('span.prov_identitas').html(data.prov_identitas);
					$('span.kec_iden').html(data.kec_identitas);
					$('span.nama_ayah').html(data.nama_lengkap_ayah);
					$('span.nama_pasangan').html(data.nama_lengkap_pasangan);
					$('span.nama_penanggung').html(data.nama_penanggung_jawab);
					$('span.kab_identitas').html(data.kabkota_identitas);
					$('span.nama_ibu').html(data.nama_lengkap_ibu);
					$('span.kel_identitas').html(data.kel_identitas);
					$('span.nohp_pasangan').html(data.hp_pasangan);
					$('span.nohp_penanggung').html(data.hp_penanggung_jawab);
					$('span.prov_identitas').html(data.provinsi_identitas);
					$('span.foto_bpjs').html('<img src="http://localhost/_13_simrs/uploads/pasien/'+data.attachment_bpjs+'" width="200" height="200" >');
					$('span.foto_identitas').html('<img src="http://localhost/_13_simrs/uploads/pasien/'+data.attachment_identitas+'" width="200" height="200" >');
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
</script>