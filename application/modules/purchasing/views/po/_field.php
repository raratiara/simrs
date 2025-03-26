									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No </label>
												<div class="col-md-8">
													<?=$txtpo;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Nilai PO </label>
												<div class="col-md-8">
													<?=$worth;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Kode Supplier</label>
												<div class="col-md-8">
													<?=$seloSupplier;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Supplier Name</label>
												<div class="col-md-8">
													<?=$txtnamasupplier;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Alamat</label>
												<div class="col-md-8">
													<?=$txtalamatsupplier;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdatepo;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Delivery Date </label>
												<div class="col-md-8">
													<?=$txtdatedue;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">#SPK/Contract </label>
												<div class="col-md-8">
													<?=$txtspk;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Purchasing PIC </label>
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
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Term</label>
												<div class="col-md-10">
													<?=$seloTerm;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Currency</label>
												<div class="col-md-10">
													<?=$seloCurrency;?>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">PO Remarks</label>
												<div class="col-md-10">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">PO Term</label>
												<div class="col-md-10">
													<?=$txtketentuan;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Detail PO </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover order-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col" style="text-align:right;">Qty</th>
																<th scope="col">Satuan</th>
																<th scope="col" style="text-align:right;">Harga Satuan</th>
																<th scope="col" style="text-align:right;">Total Harga</th>
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