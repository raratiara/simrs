                    <h3 class="page-title"></h3>
                     <div class="row">
                        <div class="col-md-12">
                            <!-- BEGIN TABLE PORTLET-->
                            <div class="portlet box blue">
                                <div class="portlet-title">
                                    <div class="caption">
                                        <i class="fa fa-database"></i><?php if (isset($title) && $title<>"") echo $title;?>
									</div>
                                    <div class="actions">
										<?php /*if  (_USER_ACCESS_LEVEL_EKSPORT == "1") { ?>
											<a class="btn btn-default btn-sm btn-circle" id="btnEksportData">
												<i class="fa fa-upload"></i>
												Eksport
											</a>
										<?php }*/ ?>
                                    </div>
                                </div>
                                <div class="portlet-body">
											<!--form action="<?php echo base_url($sfolder.'/truncate');?>" method="post" name="frmData" id="frmData">
												<table id="dynamic-table" class="table table-striped table-bordered table-hover">
													<thead>
														<tr>
															<th>ID</th>
															<th>KODE PROJECT</th>
															<th>PROJECT</th>
															<th>ANGGARAN</th>
															<th>BIAYA</th>
															<th>SELISIH</th>
															<th>STATUS</th>
															<th>TGL MULAI</th>
															<th>TGL SELESAI</th>
														</tr>
													</thead>

													<tbody>
													</tbody>

													<tfoot>
														<tr class="fieldSrc"> 
															<th>ID</th>
															<th>KODE PROJECT</th>
															<th>PROJECT</th>
															<th>ANGGARAN</th>
															<th>BIAYA</th>
															<th>SELISIH</th>
															<th>STATUS</th>
															<th>TGL MULAI</th>
															<th>TGL SELESAI</th>
														</tr>
													</tfoot>

												</table>
											</form-->
                                </div>
                            </div>
                            <!-- END TABLE PORTLET-->
                        </div>
                    </div>

					
					<div id="modal-eksport-data" class="modal fade" tabindex="-1">
						<div class="modal-dialog">
							<div class="modal-content">
								<form class="form-horizontal" method="POST" id="frmEksportData" enctype="multipart/form-data" action="<?php echo base_url($sfolder.'/eksport_action');?>">
								<div class="modal-header bg-blue bg-font-blue no-padding">
									<div class="table-header " id="title_materi_paket_sesi">
										Eksport <?php echo $smodul; ?>
									</div>
								</div>

								<div class="modal-footer no-margin-top">
									<button class="btn btn-sm blue pull-left" data-dismiss="modal">
										<i class="fa fa-times"></i>
										Tutup
									</button>
									<button class="btn btn-sm blue pull-right" id="submit-eksport-data">
										<i class="fa fa-times"></i>
										Eksport
									</button>
								</div>
								 </form>
							</div>
						</div>
					</div>
