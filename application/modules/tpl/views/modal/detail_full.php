					<!-- Modal View Detail Data -->
					<div id="modal-view-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-view-data" aria-hidden="true">
						<div class="vertical-alignment-helper">
						<div class="modal-dialog modal-full vertical-align-center">
							<div class="modal-content">
								<div class="modal-header bg-blue bg-font-blue no-padding">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
									<div class="table-header">
										Detail <?php echo $smodul; ?>
									</div>
								</div>

								<div class="modal-body" style="min-height:100px; margin:10px">
									<?php $this->load->view("_detail"); ?>
								</div>

								<div class="modal-footer no-margin-top">
									<center>
									<button class="btn blue" data-dismiss="modal">
										<i class="fa fa-times"></i>
										Close
									</button>
									</center>
								</div>
							</div>
						</div>
						</div>
					</div>
