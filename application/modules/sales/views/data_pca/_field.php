									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">No Project</label>
												<div class=" col-md-7">
												<?php echo $seloProject; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Nama Project</label>
												<div class=" col-md-7">
												<?php echo $txttitle; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Nilai Project</label>
												<div class=" col-md-7">
												<?php echo $txtworth; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Total Anggaran</label>
												<div class=" col-md-7">
												<?php echo $txtbudget; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Proyeksi Margin</label>
												<div class=" col-md-7">
												<?php echo $txtmargin; ?>
												</div>
											</div>
										</div>
									</div>
									<div class="row rab">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Detail RAB </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addrabrow" value="Add RAB" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover rab-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">NO WBS</th>
																<th scope="col">DESKRIPSI BIAYA</th>
																<th scope="col">ANGGARAN BIAYA</th>
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
									<div class="row attachment">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>ATTACHMENT </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addattcrow" value="Add Attachment" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover attc-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">File Attachment</th>
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