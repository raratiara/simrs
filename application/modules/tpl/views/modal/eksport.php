					<!-- Modal Export Data -->
					<div id="modal-eksport-data" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="modal-eksport-data" aria-hidden="true">
						<div class="vertical-alignment-helper">
						<div class="modal-dialog vertical-align-center">
							<div class="modal-content">
								<form class="form-horizontal" id="frmEksportData" enctype="multipart/form-data">
								<div class="modal-header bg-blue bg-font-blue no-padding">
									<button type="button" class="close" data-dismiss="modal" aria-hidden="true"></button>
									<div class="table-header">
										Eksport <?php echo $smodul; ?>
									</div>
								</div>
								 </form>

								<div class="modal-footer no-margin-top">
									<center>
									<button class="btn blue" id="submit-eksport-data" onclick="save()">
										<i class="fa fa-download"></i>
										Eksport
									</button>
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