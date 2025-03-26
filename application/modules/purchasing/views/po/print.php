<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order # <?=$rs->po;?></title>
    
    <style>
	body {
	  -webkit-print-color-adjust: exact !important;
	}

	@page {
		size: 21cm 29.7cm;
		margin: 30px 30px 30px 30px;
		 /* change the margins as you want them to be. */
	}
	
    @media only screen {
		.invoice-box {
			max-width: 800px;
			margin: auto;
			padding: 30px;
			border: 1px solid #eee;
			box-shadow: 0 0 10px rgba(0, 0, 0, .15);
		}

		.invoice-box {
			font-size: 11px;
			line-height: 20px;
		}

		.invoice-box table tr.top table td.title {
			font-size: 18px;
			line-height: 24px;
		}

		.invoice-box table tr.heading td {
			text-align:right;
		}

		.invoice-box table tr.heading td:first-child , .invoice-box table tr.heading td:nth-child(2){
			text-align:left;
		}

		.invoice-box table tr.heading td:nth-child(2){
			width:400px;
		}

		.label-box {
			width: 85px;
		}

		.info-box {
			width: 120px;
		}

		.info-address-box {
			width: 350px;
		}

		.label-box-in {
			width: 80px;
		}

		.info-box-in {
			width: 230px;
		}
		
		.pre-bsign{
			width: 350px;
		}
		
		.bottom-sign {
			width: 220px;
		}
	
		ul.notes li {
			font-size: 10px;
			line-height: 16px;
		}
    }

    @media print {
		.invoice-box {
			font-size: 11px;
			line-height: 20px;
		}

		.invoice-box table tr.top table td.title {
			font-size: 18px;
			line-height: 24px;
		}

		.invoice-box table tr.heading td {
			text-align:right;
		}

		.invoice-box table tr.heading td:first-child , .invoice-box table tr.heading td:nth-child(2){
			text-align:left;
		}

		.invoice-box table tr.heading td:nth-child(2){
			width:400px;
		}

		.label-box {
			width: 85px;
		}

		.info-box {
			width: 120px;
		}

		.info-address-box {
			width: 350px;
		}

		.label-box-in {
			width: 80px;
		}

		.info-box-in {
			width: 230px;
		}
		
		.pre-bsign{
			width: 350px;
		}
		
		.bottom-sign {
			width: 220px;
		}
	
		ul.notes li {
			font-size: 10px;
			line-height: 16px;
		}
	}

    .invoice-box {
        font-family: 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
        color: #555;
    }
    
    .invoice-box table {
        width: 100%;
        line-height: inherit;
        text-align: left;
    }
    
    .invoice-box table td {
        padding: 5px;
        vertical-align: top;
    }
    
    .invoice-box table tr td:nth-child(5) {
        text-align: right;
    }
    
    .invoice-box table tr.top td:nth-child(2), .invoice-box table tr.information td:nth-child(2) {
        text-align: right;
    }
    
    .invoice-box table tr.top table td {
        padding-bottom: 0px;
    }
    
    .invoice-box table tr.top table td.title {
        color: #333;
    }

    .invoice-box table tr.information table td {
        padding-bottom: 10px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        font-weight: bold;
    }
    
    .invoice-box table tr.details td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.item td{
        border-bottom: 1px solid #eee;
        border-right: 1px solid #eee;
    }

    .invoice-box table tr.item td.last {
        border-right: none;
    }
    
    .invoice-box table tr.item.last td {
        /*border-bottom: none;*/
    }
    
    .invoice-box table tr.total td:nth-child(1) {
        /*border-bottom: 2px solid #eee;*/
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-right: 2px solid #eee;
        /*border-bottom: 2px solid #eee;*/
        text-align: right;
        font-weight: bold;
    }
	
    .invoice-box table tr.total td:nth-child(3) {
        border-right: 2px solid #eee;
        border-bottom: 2px solid #eee;
        text-align: right;
        font-weight: bold;
    }
    
    .invoice-box table tr.sign td {
        text-align: center;
        width-min: 150px;
    }

    @media only screen and (max-width: 600px) {
        .invoice-box table tr.top table td {
            width: 100%;
            display: block;
            text-align: center;
        }
        
        .invoice-box table tr.information table td {
            width: 100%;
            display: block;
            text-align: center;
        }
    }
    
    /** RTL **/
    .rtl {
        direction: rtl;
        font-family: Tahoma, 'Helvetica Neue', 'Helvetica', Helvetica, Arial, sans-serif;
    }
    
    .rtl table {
        text-align: right;
    }
    
    .rtl table tr td:nth-child(5) {
        text-align: left;
    }

	.top-cont {
		margin-bottom: -20px;
	}

	.title-box {
		display:inline-block;
		width: 250px;
        text-align: left;
	}

	.title-box-reposition {
        text-align: left;
		margin-top: -10px;
		margin-left: -240px;
	}

	.comp-box-reposition {
		margin-top: -45px;
		margin-left: 125px;
	}
	
    .company {
        font-weight: bold;
        color: #1472c0;
    }
	
    .company-address {
        font-weight: bold;
    }

	.label-box {
		display:inline-block;
		font-weight: bold;
		vertical-align:top;
        text-align: left;
	}

	.info-box {
		display:inline-block;
        text-align: left;
	}

	.label-box-sign {
		display:inline-block;
		font-weight: bold;
		vertical-align:top;
		width: 45px;
        line-height: 28px;
        text-align: left;
	}

	.info-box-sign {
		display:inline-block;
		width: 200px;
        line-height: 28px;
        border-bottom: 1px solid #000;
		margin-bottom: -10px;
        text-align: left;
	}

	.label-quote {
		display:inline-block;
		vertical-align:top;
		width: 120px;
	}

	.inter {
		display:inline-block;
		vertical-align:top;
		width: 20px;
        text-align: center;
	}
	
	.info-quote {
		display:inline-block;
		font-weight: bold;
	}

	.info-address-box {
		border: 2px solid #eee;
	}
	
	.label-to {
		display:inline-block;
		background: #eee;
		font-weight: bold;
		margin-top: -5px;
		margin-left: -5px;
		padding: 5px;
		width: 100%;
	}

	.label-to-contact {
		display:inline-block;
		font-weight: bold;
		padding: 5px;
		width: 100%;
	}

	.label-to-address {
		display:inline-block;
		padding: 5px 5px 0px 5px;
		width: 100%;
	}
	
	.label-left-override {
        text-align: left;
	}

	.label-box-in {
		display:inline-block;
		vertical-align:top;
        text-align: left;
	}

	.info-box-in {
		display:inline-block;
        text-align: left;
	}
	
	.bottom-notes {
		display:inline-block;
		vertical-align:top;
	}
	
	.bottom-inter {
		display:inline-block;		
		width: 70px;
	}
	
	.bottom-sign {
		display:inline-block;		
		vertical-align:top;
		padding-top: 0px;
		font-weight: bold;
		text-align: center;
	}
	
	.bsign{
		text-align: center;
	}

	.remark-box {
		display:inline-block;
		margin-top: -5px;
		padding: 5px;
		border: 2px solid #eee;
		width: 100%;
		min-height: 70px;
	}
	
	ul.notes li {
		margin-left : -25px;
	}

	.qrcode-reposition {
		margin-top: -55px;
	}
	
	.new_page {
	  break-before: page;
	  margin-top: 10px;
	}

	.not_loose_after {
	  break-after: avoid-page;
	}

	.not_split_section {
	  break-inside: avoid;
	}
	</style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="6">
					<div class="top-cont">
                    <table>
                        <tr>
                            <td class="title">
                                <img width="109" src="<?php echo _ASSET_LOGO_FRONT; ?>" alt="<?php echo _COMPANY_NAME; ?>" />
                            </td>
                            <td class="title">
                                <span class="title-box">Purchase Order</span>
                            </td>
                        </tr>
                    </table>
					</div>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
								<div class="comp-box-reposition">
                                <span class="company"><?=_COMPANY_NAME;?></span><br>
                                <span class="company-address">Ruko Prima Orchard Apartemen Blok C5/F6<br/>
								Jl Raya Perjuangan No. 1, Harapan Baru, Kota Bekasi<br/>Jawa Barat 17123 - Indonesia<br/>
								Phone : (021) 8888 9442<br/>
								NPWP  : 73.253.177.7-407.000<br/>
								www.nathabuana.id</span>
								</div>
                            </td>
                            <td>
								<div class="title-box-reposition">
                                <span class="label-box">No</span><span class="inter">:</span><span class="info-box"><?=$rs->po;?></span><br/>
                                <span class="label-box">Date</span><span class="inter">:</span><span class="info-box"><?=date('F j, Y',$rs->date_po);?></span><br/>
                                <span class="label-box">Currency</span><span class="inter">:</span><span class="info-box"><?=isset($rs->currency)?$rs->currency:'';?></span><br/>
                                <span class="label-box">Delivery Date</span><span class="inter">:</span><span class="info-box"><?=date('F j, Y',$rs->date_due);?></span><br/>
                                <span class="label-box">Page</span><span class="inter">:</span><span class="info-box">1 of 1</span><br/>
                                <span class="label-box">#SPK/Contract</span><span class="inter">:</span><span class="info-box"><?=$rs->spk;?></span>
								</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td class="info-address-box">
								<span class="label-to">PURCHASE TO :</span><br/>
                                <span class="label-to-address">
									<span class="label-box-in">SID</span><span class="inter">:</span><span class="info-box-in"><?=isset($rs->supplier_sid)?$rs->supplier_sid:'';?></span><br/>
									<span class="label-box-in">Supplier Name</span><span class="inter">:</span><span class="info-box-in"><?=isset($rs->supplier_name)?'<strong>'.$rs->supplier_name.'<br/></strong>':'';?><?=isset($rs->supplier_address)?nl2br($rs->supplier_address):'';?></span><br/>
									<span class="label-box-in">Att</span><span class="inter">:</span><span class="info-box-in"><?=isset($rs->supplier_contact)?$rs->supplier_contact:'';?></span>
								</span/>
                            </td>
                            <td>
                            </td>
                            <td class="info-address-box">
								<span class="label-to label-left-override">DELIVERY TO :</span><br/>
                                <span class="label-to-contact label-left-override"><?=_COMPANY_NAME;?></span><br/>
                                <span class="label-to-address label-left-override">Ruko Prima Orchard Apartemen Blok C5/F6<br/>
								Jl. Raya Perjuangan No.1, Harapan Baru<br/>Kota Bekasi, Jawa Barat 17123</span/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>            
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6">Please supply the following items :</td>
            </tr>
            <tr class="heading">
                <td>No</td>
                <td>Descriptions of Goods</td>
                <td>Unit</td>
                <td>Qty</td>
                <td>Unit Price</td>
                <td>Amount</td>
            </tr>
            <?=$rs2[0];?>
			<?php
			if($rs->worth > 0){
			?>
            <tr class="total">
                <td colspan="4"></td>
                <td>
                   Total
                </td>
                <td>
                   <?=number_format($rs->worth,2,',','.');?>
                </td>
            </tr>
			<?php
			}
			?>
            <tr>
                <td colspan="6">Term of payment : <?=isset($rs->term)?$rs->term:'';?><br/>Remarks,</td>
            </tr>
            <tr>
                <td colspan="4">
					<span class="remark-box"><?=nl2br($rs->description);?></span>
				</td>
                <td colspan="2">
				</td>
            </tr>
            <tr>
                <td colspan="4" class="pre-bsign">Supplier Confirmation:<br/>We acknowledge receipt of this Purchase Order and confirm our compliance<br/>with the details and Other Terms and Condition behind this page.</td>
                <td colspan="2" class="bsign">
					<span class="bottom-sign">Authorized Person,<br/><br/>ANUNG WICAKSONO</span>
				</td>
            </tr>
            <tr>
                <td colspan="4">
                    <span class="label-box-sign">Name</span><span class="inter">:</span><span class="info-box-sign"></span><br/>
                    <span class="label-box-sign">Date</span><span class="inter">:</span><span class="info-box-sign"></span><br/>
                    <span class="label-box-sign">Sign</span><span class="inter">:</span><span class="info-box-sign"><br/><br/></span>
                </td>
                <td colspan="2" class="bsign">
					<span class="bottom-sign">PURCHASER : <?=isset($rs->purchasing_pic)?strtoupper($rs->purchasing_pic):'';?><br/><br/><i style="font-weight: normal;">ELECTRONICALLY APPROVED</i></span>
                </td>
            </tr>
            <tr>
                <td colspan="4">
					<ul class="notes">
						<li>The PO Number must appear on Delivery Order and Invoice.</li>
						<li>For the sake of optimal communication, please sign our purchase order and return to us by email.</li>
					</ul>
				</td>
                <td colspan="2" class="bsign">
					<div class="qrcode-reposition">
					<span class="bottom-sign"><?=isset($rs->qrpath)?'<img width="100px" src="'.$rs->qrpath.'" /><br><div class="txt-qrcode">'.$rs->po.'</div>':'';?></span></td>
					</div>
				</td>
            </tr>
        </table>
    </div>
	<div class="new_page"></div>
	<br/>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="6">
					<div class="top-cont">
                    <table>
                        <tr>
                            <td class="title">
                                <img width="109" src="<?php echo _ASSET_LOGO_FRONT; ?>" alt="<?php echo _COMPANY_NAME; ?>" />
                            </td>
                            <td class="title">
                            </td>
                        </tr>
                    </table>
					</div>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
								<div class="comp-box-reposition">
                                <span class="company"><?=_COMPANY_NAME;?></span><br>
                                <span class="company-address">Ruko Prima Orchard Apartemen Blok C5/F6<br/>
								Jl Raya Perjuangan No. 1, Harapan Baru, Kota Bekasi<br/>Jawa Barat 17123 - Indonesia<br/>
								Phone : (021) 8888 9442<br/>
								NPWP  : 73.253.177.7-407.000<br/>
								www.nathabuana.id</span>
								</div>
                            </td>
                            <td></td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6">
					<?=nl2br($rs->notes);?>
				</td>
            </tr>
        </table>
    </div>
</body>
</html>