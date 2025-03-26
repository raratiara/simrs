									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">No Project</label>
												<div class=" col-md-7">
												<?php echo $txtproject; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Nama Project</label>
												<div class=" col-md-7">
												<?php echo $txttitle; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Kode Customer</label>
												<div class=" col-md-7">
												<?php echo $seloCustomer; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">No SPK/Kontrak</label>
												<div class=" col-md-7">
												<?php echo $seloSpk; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Plan Tgl Start</label>
												<div class=" col-md-7">
												<?php echo $txtdatepstart; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Actual Start</label>
												<div class=" col-md-7">
												<?php echo $txtdateastart; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Plan Tgl Finish</label>
												<div class=" col-md-7">
												<?php echo $txtdatepfinish; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Actual Finish</label>
												<div class=" col-md-7">
												<?php echo $txtdateafinish; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Project Scope</label>
												<div class=" col-md-7">
												<?php echo $seloProjectScope; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">SLA</label>
												<div class=" col-md-7">
												<?php echo $seloSla; ?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Sales PIC</label>
												<div class=" col-md-7">
												<?php echo $seloPic; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">PM</label>
												<div class=" col-md-7">
												<?php echo $seloPM; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">GM</label>
												<div class=" col-md-7">
												<?php echo $seloGM; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Admin</label>
												<div class=" col-md-7">
												<?php echo $seloADM; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Tipe Project</label>
												<div class=" col-md-7">
												<?php echo $radiotype; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Prepared By</label>
												<div class=" col-md-7">
												<?php echo $seloDept; ?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-5 control-label no-padding-right">Project Status</label>
												<div class=" col-md-7">
												<?php echo $seloStatus; ?>
												</div>
											</div>
										</div>
									</div>
									<div class="row member">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Data Project Member </div>
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
									<div class="row po-wo">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Data PO/WO </div>
													<div class="tools">
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover po-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">No PO</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Date</th>
																<th scope="col">Nilai</th>
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