					<!-- Modal Form Data -->
					<div id="modal-form-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-form-data" aria-hidden="true">
						<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-center">
							<div class="modal-content">
								<form class="form-horizontal" id="frmInputData" enctype="multipart/form-data">
								<div class="modal-header bg-blue bg-font-blue no-padding">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
									<div class="table-header">
										<span id="mfdata"></span> <?php echo $smodul; ?>
									</div>
								</div>

								<div class="modal-body" style="min-height:100px; margin:10px">
									<input type="hidden" name="id" value="">
									<?php $this->load->view("_field"); ?>
								</div>
								</form>

								<div class="modal-footer no-margin-top">
									<span class="act-container-btn">
										<button class="btn btn-info" id="submit-data" onclick="save()">
											<i class="fa fa-check"></i>
											Save
										</button>
										<button class="btn" onclick="reset()">
											<i class="fa fa-undo"></i>
											Reset
										</button>
									</span>
									<button class="btn blue" data-dismiss="modal">
										<i class="fa fa-times"></i>
										Close
									</button>
								</div>
							</div>
						</div>
						</div>
					</div>
