<script type="text/javascript">
	var module_path = "<?php echo base_url($folder_name); ?>"; //for save method string
	var myTable;
	var validator;
	var save_method; //for save method string
	var idx; //for save index string
	var ldx; //for save list index string

	<?php if (_USER_ACCESS_LEVEL_VIEW == "1") { ?>
		jQuery(function($) {
			/* load table list */
			myTable =
				$('#dynamic-table')
				.DataTable({
					fixedHeader: {
						headerOffset: $('.page-header').outerHeight()
					},
					responsive: true,
					bAutoWidth: false,
					"aoColumnDefs": [{
							"bSortable": false,
							"aTargets": [0, 1]
						},
						{
							"sClass": "text-center",
							"aTargets": [0, 1]
						}
					],
					"aaSorting": [
						[2, 'asc']
					],
					"sAjaxSource": module_path + "/get_data",
					"bProcessing": true,
					"bServerSide": true,
					"pagingType": "bootstrap_full_number",
					"colReorder": true,
					"searchCols": [
						null,
						null,
						null,
						{
							"search": document.getElementById('id_project_filter').value
						},
						{
							"search": document.getElementById('id_engineer_filter').value
						},
						null,
						{
							"search": document.getElementById('location_filter').value
						}
					],
				});

			<?php if (_USER_ACCESS_LEVEL_ADD == "1" || _USER_ACCESS_LEVEL_UPDATE == "1") { ?>
				validator = $("#frmInputData").validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block help-block-error', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					ignore: "", // validate all fields including form hidden input
					rules: {
						id_project: {
							required: true
						},
						approval: {
							required: true
						}
					},
					messages: { // custom messages for radio buttons and checkboxes
					},
					errorPlacement: function(error, element) { // render error placement for each input type
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
					highlight: function(element) { // hightlight error inputs
						$(element)
							.closest('.form-group').addClass('has-error'); // set error class to the control group
					},
					unhighlight: function(element) { // revert the change done by hightlight
						$(element)
							.closest('.form-group').removeClass('has-error'); // set error class to the control group
					},
					success: function(label) {
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

			<?php if (_USER_ACCESS_LEVEL_DELETE == "1") { ?>
				//check all
				$("#check-all").click(function() {
					$(".data-check").prop('checked', $(this).prop('checked'));
				});
			<?php } ?>
		})

		<?php $this->load->view(_TEMPLATE_PATH . "common_module_js"); ?>
	<?php } ?>

	<?php if (_USER_ACCESS_LEVEL_VIEW == "1" && (_USER_ACCESS_LEVEL_UPDATE == "1" || _USER_ACCESS_LEVEL_DETAIL == "1")) { ?>

		function load_data() {
			$.ajax({
				type: "POST",
				url: module_path + '/get_detail_data',
				data: {
					id: idx
				},
				cache: false,
				dataType: "JSON",
				success: function(data) {
					if (data != false) {
						if (save_method == 'update') {
							$('[name="id"]').val(data.id);
							$('[name="id_project"]').val(data.id_project).trigger('change.select2');
							$('[name="id_engineer"]').val(data.id_engineer).trigger('change.select2');
							$('[name="location"]').val(data.location);
							$('[name="activity"]').val(data.activity);
							$('[name="note"]').val(data.note);
							$('[name="remark"]').val(data.remark);
							$('[name="date"]').val(data.dt);
							$('[name="time_in"]').val(data.time_in);
							$('[name="time_out"]').val(data.time_out);
							$('[name="approval"][value="' + data.approval + '"]').prop('checked', true);
							$.uniform.update();
							$('#mfdata').text('Update');
							$('#modal-form-data').modal('show');
						}
						if (save_method == 'detail') {
							$('span.location').html(data.location);
							$('span.project').html(data.project);
							$('span.engineer').html(data.engineer);
							$('span.activity').html(data.activity);
							$('span.dataNote').html(data.note);
							$('span.remark').html(data.remark);
							$('span.dt').html(data.dt);
							var approval = 'Prepared';
							if (data.approval == 1) {
								approval = 'Approver';
							}
							$('span.approval').html(approval);
							$('span.time_in').html(data.time_in);
							$('span.time_out').html(data.time_out);
							$('#modal-view-data').modal('show');
						}
					} else {
						title = '<div class="text-center" style="padding-top:20px;padding-bottom:10px;"><i class="fa fa-exclamation-circle fa-5x" style="color:red"></i></div>';
						btn = '<br/><button class="btn blue" data-dismiss="modal">OK</button>';
						msg = '<p>Gagal peroleh data.</p>';
						var dialog = bootbox.dialog({
							message: title + '<center>' + msg + btn + '</center>'
						});
						if (response.status) {
							setTimeout(function() {
								dialog.modal('hide');
							}, 1500);
						}
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
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

	/* additional action */
	/* Filter Project */
	$('#frmFilterData').on('change', 'select#id_project_filter', function(event) {
		myTable.column( 3 )
            .search( $(this).val() )
            .draw();
	});

	/* Filter Engineer */
	$('#frmFilterData').on('change', 'select#id_engineer_filter', function(event) {
		myTable.column( 4 )
            .search( $(this).val() )
            .draw();
	});

	/* Filter Location */
	$('#location_filter').off('keyup change')
        .on('keyup change', function (e) {
			myTable.column( 6 )
            	.search( $(this).val() )
            	.draw();
		}
	);

	// Refilter Periode -> masih belum bisa
	$('#frmFilterData').on('change', 'select#periodstart', function(event) {
		myTable.draw();
	});

	$('#frmFilterData').on('change', 'select#periodend', function(event) {
		myTable.draw();
	});

	// Date range filter
	minDateFilter = "";
	maxDateFilter = "";

	$.fn.dataTable.ext.search.push(
		function( settings, data, dataIndex ) {
			var min  = $('#periodstart').val();
			var max  = $('#periodend').val();
			var createdAt = data[7]; // Our date column in the table

			if  ( 
					( min == "" || max == "" )
					|| 
					( moment(createdAt).isSameOrAfter(min) && moment(createdAt).isSameOrBefore(max) ) 
				)
			{
				return true;
			}
			return false;
		}
	);
</script>