									<div class="row">
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No PR </label>
												<div class="col-md-8">
													<?=$seloPRca;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">No Project </label>
												<div class="col-md-8">
													<?=$txtnoproject;?>
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
													<?=$txtwbs;?>
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
													<?=$txtasdraft;?><?=$txtasoldstat;?><?=$seloLastStatus;?>
												</div>
											</div>
										</div>
										<div class="col-md-6 col-sm-12">
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Date </label>
												<div class="col-md-8">
													<?=$txtdateclosing;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Requestor Name </label>
												<div class="col-md-8">
													<?=$txtrequestor;?>
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
												<label class="col-md-4 control-label no-padding-right">Total PR </label>
												<div class="col-md-8">
													<?=$txttotalpr;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total Adjustment </label>
												<div class="col-md-8">
													<?=$txttotaladj;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Total Closing </label>
												<div class="col-md-8">
													<?=$txttotalclosing;?>
												</div>
											</div>
											<div class="form-group">
												<label class="col-md-4 control-label no-padding-right">Balance Closing </label>
												<div class="col-md-8">
													<?=$txtbalanceclosing;?>
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
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addcarow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover ca-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
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
									<div class="row adj">
                                        <div class="col-md-12">
											<div class="portlet box green">
												<div class="portlet-title">
													<div class="caption"><i class="fa fa-cubes"></i>Payment Adjustment </div>
													<div class="tools">
														<input type="button" class="btn btn-default blue btn-outline btn-circle btn-sm active" id="addadjrow" value="Add Row" />
													</div>
												</div>
												<div class="portlet-body">
													<div class="table-scrollable tablesaw-cont">
													<table class="table table-striped table-bordered table-hover adj-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
														<thead>
															<tr>
																<th scope="col">No</th>
																<th scope="col">Date</th>
																<th scope="col">Deskripsi</th>
																<th scope="col">Tipe</th>
																<th scope="col">Metode</th>
																<th scope="col">Jumlah</th>
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
													<table class="table table-striped table-bordered table-hover ca-log-list tablesaw tablesaw-stack" data-tablesaw-mode="stack">
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