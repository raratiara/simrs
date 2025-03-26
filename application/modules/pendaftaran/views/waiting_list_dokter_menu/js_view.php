<script type="text/javascript">
var module_path = "<?php echo base_url($folder_name);?>"; //for save method string
var myTable;
var validator;
var save_method; //for save method string
var pout = false; //for printing
var idx; //for save index string
var ldx; //for save list index string
var modloc = '/_13_simrs/pendaftaran/waiting_list_dokter_menu/';
var opsForm = 'form#frmInputData';
var locate = 'table.ca-list';
var dlocate = 'table.dca-list';
var wcount = 0; //for ca list row identify
var plocate = 'table.ca-log-list';
var pdlocate = 'table.dca-log-list';
var pwcount = 0; //for ca log list row identify


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
					$('[name="id_pendaftaran"]').val(data.id_pendaftaran);
					$('[name="status"]').val(data.status);
					$('[name="no_rm"]').val(data.no_rekam_medis);
					$('[name="tgl_lahir"]').val(data.tgl_lahir);
					$('[name="nama_pasien"]').val(data.pasien);
					$('[name="poli"]').val(data.poli);
					$('[name="dokter"]').val(data.dokter);
					$('[name="tinggi_badan"]').val(data.tinggi_badan);
					$('[name="suhu_tubuh"]').val(data.suhu_tubuh);
					$('[name="tekanan_darah"]').val(data.tekanan_darah);
					$('[name="saturasi"]').val(data.saturasi);

					$('[name="berat_badan"]').val(data.berat_badan);
					$('[name="denyut_nadi"]').val(data.denyut_nadi);
					$('[name="frekuensi_napas"]').val(data.frekuensi_napas);
					$('[name="tingkat_nyeri"]').val(data.tingkat_nyeri);
					$('[name="wawancara"]').val(data.wawancara_medis);
					$('[name="diagnosa"]').val(data.diagnosa);
					
					$('[name="pemeriksaan_penunjang"][value="'+data.pemeriksaan_penunjang+'"]').prop('checked', true);


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


function chgKode(idx){
	var idobat = $('select[name="kode['+idx+']"]').val(); 
	
	$.ajax({
		type: "POST",
        url : module_path+'/get_detail_data_obat',
		data: { 'id': idobat },
		cache: false,		
        dataType: "JSON",
        success: function(data)
        { 
			if(data != false){ console.log(data);
				$('[name="nama['+idx+']"]').val(data[0]['nama']);
				$('[name="satuan['+idx+']"]').val(data[0]['satuan']);
				$('[name="hdnharga['+idx+']"]').val(data[0]['harga']);
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

$("#addcarow").on("click", function () {
	expire();
	var newRow = $("<tr>");
	$.ajax({type: 'post',url: module_path+'/genexpensesrow',data: { count:wcount },success: function (response) {
			newRow.append(response);
			$(locate).append(newRow);
			wcount++;
			/*$(locate+' [name="qty['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="price['+wcount+']"]').number(true,0,',','.');
			$(locate+' [name="jumlah['+wcount+']"]').number(true,0,',','.');*/
		}
	}).done(function() {
		tSawBclear('table.order-list');
	});
});



function del(idx,hdnid){
	
	if(hdnid != ''){ //delete dr table

		$.ajax({type: 'post',url: module_path+'/delrowResep',data: { id:hdnid },success: function (response) {
				
			}
		}).done(function() {
			tSawBclear('table.order-list');
		});

	}

	//delete tampilan row

	var table = document.getElementById("tblResep");
	table.deleteRow(idx);
	

}




<?php } ?>


</script>