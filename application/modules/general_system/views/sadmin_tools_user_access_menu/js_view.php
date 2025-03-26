<script type="text/javascript">
	jQuery(function($) {

		$("#btnEksportData").click(function() {
			$('#modal-eksport-data').modal({show:true});
		});
		$("#submit-eksport-data").click(function() {
			var data_post = $('#btnEksportData').serialize();
			$.ajax({
			    type: "POST",
			    url: form.attr( 'action' ),
			    data: data_post,
			    success: function( response ) {
			        console.log( response );
			        $('#modal-eksport-data').modal({show:false});
			    },
			    error: function() {
			        alert('error handing here');
			    }
		    });
			return false;
		});

		$("#btnImportData").click(function() {
			$('#modal-import-data').modal({show:true});
		});
		
		$("#submit-import-data").click(function() {
			var data_post = $('#frmImportData').serialize();

			$.ajax({
				xhr: function () {
			        var xhr = new window.XMLHttpRequest();
			        xhr.upload.addEventListener("progress", function (evt) {
			            if (evt.lengthComputable) {
			                var percentComplete = evt.loaded / evt.total;
			                console.log(percentComplete);
			                $('.progress').css({
			                    width: percentComplete * 100 + '%'
			                });
			                if (percentComplete === 1) {
			                    $('.progress').addClass('hide');
			                }
			            }
			        }, false);
			        xhr.addEventListener("progress", function (evt) {
			            if (evt.lengthComputable) {
			                var percentComplete = evt.loaded / evt.total;
			                console.log(percentComplete);
			                $('.progress').css({
			                    width: percentComplete * 100 + '%'
			                });
			            }
			        }, false);
			        return xhr;
			    },
			    type: "POST",
			    url: form.attr( 'action' ),
			    data: data_post,
			    success: function( response ) {
			        console.log( response );
			    },
			    error: function() {
			        alert('error handing here');
			    }
		    });
			return false;
		});

		$('#dynamic-table').on('click','a[data-confirm]',function(ev){
			var href = $(this).attr('href');
			if (!$('#dataConfirmModal').length){
				$('body').append('<div id="dataConfirmModal" class="modal fade" tabindex="-1"><div class="modal-dialog"><div class="modal-content"><div class="modal-header bg-blue bg-font-blue no-padding"><div class="table-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="white">&times;</span></button>KONFIRMASI</div></div><div class="modal-body" style="height:100px;">Apakah anda yakin akan menghapus data ini?</div><div class="modal-footer no-margin-top"><button class="btn btn-sm btn-danger pull-left" data-dismiss="modal"><i class="ace-icon fa fa-times"></i>Batal</button><a class="btn btn-danger" id="dataConfirmOK">OK</a></div></div></div></div>');
			}
			$('#dataConfirmModal').find('.modal-body').text($(this).attr('data-confirm'));
			$('#dataConfirmOK').attr('href', href);
			$('#dataConfirmModal').modal({show:true});
			return false;
		});

		var myTable =
		$('#dynamic-table')
		.DataTable( {
			fixedHeader: {
				headerOffset: $('.page-header').outerHeight()
			},
			responsive: true,
			bAutoWidth: false,
			"aoColumnDefs": [
			  { "bSortable": false, "aTargets": [ 0 ] },
			  { "sClass": "text-center", "aTargets": [ 0 ] }
			],
			"aaSorting": [
			  	[1,'asc'] 
			],
			"sAjaxSource": "<?php echo base_url('general_system/sadmin_tools_user_access_menu/get_data');?>",
			"bProcessing": true,
	        "bServerSide": true,
			"pagingType": "bootstrap_full_number",
			"colReorder": true,
			select: {
				style: 'multi'
			}
	    } );

		myTable.on( 'select', function ( e, dt, type, index ) {
			if ( type === 'row' ) {
				$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', true);
			}
		} );

		myTable.on( 'deselect', function ( e, dt, type, index ) {
			if ( type === 'row' ) {
				$( myTable.row( index ).node() ).find('input:checkbox').prop('checked', false);
			}
		} );


		/////////////////////////////////
		//table checkboxes
		$('th input[type=checkbox], td input[type=checkbox]').prop('checked', false);

		//select/deselect all rows according to table header checkbox
		$('#dynamic-table > thead > tr > th input[type=checkbox], #dynamic-table_wrapper input[type=checkbox]').eq(0).on('click', function(){
			var th_checked = this.checked;//checkbox inside "TH" table header

			$('#dynamic-table').find('tbody > tr').each(function(){
				var row = this;
				if(th_checked) myTable.row(row).select();
				else  myTable.row(row).deselect();
			});
		});

		//select/deselect a row when the checkbox is checked/unchecked
		$('#dynamic-table').on('click', 'td input[type=checkbox]' , function(){
			var row = $(this).closest('tr').get(0);
			if(this.checked) myTable.row(row).deselect();
			else myTable.row(row).select();
		});

		$(document).on('click', '#dynamic-table .dropdown-toggle', function(e) {
			e.stopImmediatePropagation();
			e.stopPropagation();
			e.preventDefault();
		});

		//add tooltip for small view action buttons in dropdown menu
		$('[data-rel="tooltip"]').tooltip({placement: tooltip_placement});

		//tooltip placement on right or left
		function tooltip_placement(context, source) {
			var $source = $(source);
			var $parent = $source.closest('table')
			var off1 = $parent.offset();
			var w1 = $parent.width();

			var off2 = $source.offset();
			//var w2 = $source.width();

			if( parseInt(off2.left) < parseInt(off1.left) + parseInt(w1 / 2) ) return 'right';
			return 'left';
		}

		$('.show-details-btn').on('click', function(e) {
			e.preventDefault();
			$(this).closest('tr').next().toggleClass('open');
			$(this).find(ace.vars['.icon']).toggleClass('fa-angle-double-down').toggleClass('fa-angle-double-up');
		});
	})
</script>