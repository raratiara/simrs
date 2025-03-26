									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No RB </label>
												<div class="col-md-8">
													<?=$txtreim;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$seloProject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Nama Project </label>
												<div class="col-md-8">
													<?=$txtproject;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">WBS </label>
												<div class="col-md-8">
													<?=$seloWbs;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Business Purposed </label>
												<div class="col-md-8">
													<?=$txtdescription;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Last Status </label>
												<div class="col-md-8">
													<?=$txtasdraft;?><?=$txtasoldstat;?><?=$txtasnewstat;?><?=$seloLastStatus;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdateca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Requestor Name </label>
												<div class="col-md-8">
													<?=$seloRequestor;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">NIK </label>
												<div class="col-md-8">
													<?=$txtnik;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Departemen </label>
												<div class="col-md-8">
													<?=$txtdept;?><?=$txtdepartemen;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total Request </label>
												<div class="col-md-8">
													<?=$txttotalreim;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Transfer to </label>
												<div class="col-md-8">
													<?=$radioactive;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Rekening </label>
												<div class="col-md-8">
													<?=$txtnorek;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Nama Rekening </label>
												<div class="col-md-8">
													<?=$txtnamarek;?>
												</div>
											</div>
										</div>
									</div>
									<div class="row ca">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Expenses Detail </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addreimrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover reim-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Date</th>
																<th scope="col">Deskripsi</th>
																<th scope="col" style="text-align:right;">Qty</th>
																<th scope="col">Satuan</th>
																<th scope="col" style="text-align:right;">Harga Satuan</th>
																<th scope="col">Jumlah</th>
																<th scope="col">Post Budget</th>
																<th scope="col">Attach</th>
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
									<div class="row ca-log">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>LOG Status </div>
													<div class="tools">
														<!--input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcacloserow" value="Add Row" /-->
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover reim-log-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Date</th>
																<th scope="col">PIC</th>
																<th scope="col">Attachment</th>
																<!--th scope="col"></th-->
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
									<h4 class="form-section new-stat">Approval Supporting Document</h4>
									<div class="row new-stat">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding">File </label>
												<div class="col-md-8">
													<?=$filenewstatus;?>
												</div>
											</div>
										</div>
									</div>