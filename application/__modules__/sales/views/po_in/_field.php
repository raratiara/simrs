									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Quotation </label>
												<div class="col-md-8">
													<?=$seloQuotation;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$seloProject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Kode Customer</label>
												<div class="col-md-8">
													<?=$seloCustomer;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Customer Name</label>
												<div class="col-md-8">
													<?=$txtnamacustomer;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Alamat</label>
												<div class="col-md-8">
													<?=$txtalamatcustomer;?>
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
												<label class="col-md-4 control-label no-padding-right">Due Date </label>
												<div class="col-md-8">
													<?=$txtdatedue;?>
												</div>
											</div>
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
												<label class="col-md-4 control-label no-padding-right">Sales PIC </label>
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
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Tax</label>
												<div class="col-md-8">
													<?=$txttax;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<!--label class="col-md-2 control-label no-padding-right">Tax Other</label-->
												<div class="col-md-6">
													<?=$txttaxother;?>
												</div>
											</div>
										</div>
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">TOP</label>
												<div class="col-md-10">
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">DP</label>
															<div class="col-md-12"><?=$txttopdp;?></div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">Term 1</label>
															<div class="col-md-12"><?=$txttopt1;?></div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">Term 2</label>
															<div class="col-md-12"><?=$txttopt2;?></div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">Term 3</label>
															<div class="col-md-12"><?=$txttopt3;?></div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">Final Term</label>
															<div class="col-md-12"><?=$txttoptf;?></div>
														</div>
													</div>
													<div class="col-md-2">
														<div class="form-group">
															<label class="col-md-12">Retensi</label>
															<div class="col-md-12"><?=$txttoprt;?></div>
														</div>
													</div>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">PO Descriptions</label>
												<div class="col-md-10">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">PO Note</label>
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