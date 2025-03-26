									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group tinvoice">
												<label class="col-md-4 control-label no-padding-right">Invoice Template </label>
												<div class="col-md-8">
													<?=$seloTemplateInvoice;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No PO/WO </label>
												<div class="col-md-8">
													<?=$seloPO;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Kode Customer</label>
												<div class="col-md-8">
													<?=$seloCustomer;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Bill To</label>
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
												<label class="col-md-4 control-label no-padding-right">Date Invoice </label>
												<div class="col-md-8">
													<?=$txtdateinvoice;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Proforma Invoice </label>
												<div class="col-md-8">
													<?=$txtinvoice;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Due Date </label>
												<div class="col-md-8">
													<?=$txtdatedue;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total Invoice </label>
												<div class="col-md-8">
													<?=$worth;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$seloProject;?>
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
												<label class="col-md-4 control-label no-padding-right">Bank Account</label>
												<div class="col-md-8">
													<?=$seloBankAccount;?>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
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
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Faktur </label>
												<div class="col-md-8">
													<?=$txtfaktur;?>
												</div>
											</div>
										</div>
										<div class="clearfix"></div>
										<div class="col-md-12 col-sm-12">
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Invoice Descriptions</label>
												<div class="col-md-10">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Invoice Note</label>
												<div class="col-md-10">
													<?=$txtketentuan;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-2 control-label no-padding-right">Terbilang</label>
												<div class="col-md-10">
													<?=$txtterbilang;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Status</label>
												<div class="col-md-8">
													<?=$seloStatus;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Detail Invoice </div>
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