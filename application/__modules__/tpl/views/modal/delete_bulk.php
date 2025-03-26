					<!-- Modal Bulk Delete Data -->
					<div id="modal-delete-bulk-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-delete-data" aria-hidden="true">
						<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-center">
							<div class="modal-content">
								<form class="form-horizontal" id="frmBulkDeleteData">
								<div class="modal-header bg-blue bg-font-blue no-padding">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
									<div class="table-header">
										Delete <?php echo $smodul; ?>
									</div>
								</div>

								<div class="modal-body" style="min-height:100px; margin:10px">
									<p class="text-center">Are you sure to delete data <?=$smodul;?> with ID : <span id="ids"></span></p>
									<input type="hidden" name="id" value="">
								</div>
								 </form>

								<div class="modal-footer no-margin-top">
									<center>
									<button class="btn blue" id="submit-delete-data" onclick="save()">
										<i class="fa fa-check"></i>
										Ok
									</button>
									<button class="btn blue" data-dismiss="modal">
										<i class="fa fa-times"></i>
										Cancel
									</button>
									</center>
								</div>
							</div>
						</div>
						</div>
					</div>