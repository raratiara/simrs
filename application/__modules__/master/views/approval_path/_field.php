									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-3 control-label no-padding-right">Menu <span class="required">*</span></label>
												<div class="col-md-9">
													<?=$seloMenu;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label no-padding-right">Approval <span class="required">*</span></label>
												<div class="col-md-9">
													<?=$seloApproval;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-3 control-label no-padding-right">Active</label>
												<div class="col-md-9">
													<?=$radioactive;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row member">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Data Approval Member </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addmemberrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover member-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Nama</th>
																<th scope="col">Jabatan</th>
																<th scope="col">Tlp</th>
																<th scope="col">Email</th>
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