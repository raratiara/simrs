									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No CA </label>
												<div class="col-md-8">
													<?=$txtca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$seloProject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">PIC </label>
												<div class="col-md-8">
													<?=$seloPic;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Status </label>
												<div class="col-md-8">
													<?=$seloStatus;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total CA </label>
												<div class="col-md-8">
													<?=$txttotalca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdateca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total CLOSING </label>
												<div class="col-md-8">
													<?=$txttotalclose;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdateclose;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row ca">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>CA Request </div>
													<div class="tools">
														<!--input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcarow" value="Add Row" /-->
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover ca-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">WBS</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Satuan Biaya</th>
																<th scope="col">Qty</th>
																<th scope="col">Total Biaya</th>
																<th scope="col"></th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
														</tfoot>
													</table>
													</div>
												</div>
											</div>
										</div>
									</div>
									<div class="row ca-close">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>CA Close </div>
													<div class="tools">
														<!--input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcacloserow" value="Add Row" /-->
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover ca-close-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">WBS</th>
																<th scope="col">Tgl</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Satuan Biaya</th>
																<th scope="col">Qty</th>
																<th scope="col">Total Biaya</th>
																<th scope="col"></th>
															</tr>
														</thead>
														<tbody>
														</tbody>
														<tfoot>
														</tfoot>
													</table>
													</div>
												</div>
											</div>
										</div>
									</div>