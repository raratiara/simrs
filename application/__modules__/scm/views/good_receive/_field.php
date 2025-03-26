<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">No DO <span class="required">*</span></label>
	<div class="col-md-9">
		<?= $txtdo; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Date Receive<span class="required">*</span></label>
	<div class="col-md-9">
		<?= $txtdate; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">PO </label>
	<div class="col-md-9">
		<?= $seloPo; ?>
	</div>
</div>
<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Project </label>
	<div class="col-md-9">
		<?= $seloProject; ?>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Shipper Data:</label>
	<div class="col-md-9">
		<?= $datapic; ?>
		<div style="display:block;padding-left:15px;padding-right:15px;">
			<div class="form-group">
				<label>Nama </label>
				<?= $txtshpname; ?>
			</div>
			<div class="form-group">
				<label>Alamat</label>
				<?= $txtshpaddress; ?>
			</div>
			<div class="form-group">
				<label>Telp <span class="required">*</span></label>
				<?= $txtshptelp; ?>
			</div>
		</div>
	</div>
</div>

<div class="form-group">
	<label class="col-md-3 control-label no-padding-right">Note </label>
	<div class="col-md-9">
		<?= $txtnote; ?>
	</div>
</div>


<div class="row">
	<div class="col-md-12">
		<div class="portlet box green">
			<div class="portlet-title">
				<div class="caption"><i class="fa fa-cubes"></i>List Item </div>
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
								<th scope="col">Item Code</th>
								<th scope="col">Deskripsi</th>
								<th scope="col" style="text-align:center;">Satuan</th>
								<th scope="col" style="text-align:center;">Qty</th>
								<th scope="col" style="text-align:center;">Coly</th>
								<th scope="col">Remark</th>
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