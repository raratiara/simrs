<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order # <?=$rs->po;?></title>
    
    <style>
	body {
	  -webkit-print-color-adjust: exact !important;
	}

    .invoice-box {
        max-width: 800px;
        margin: auto;
        padding: 30px;
        border: 1px solid #eee;
        box-shadow: 0 0 10px rgba(0, 0, 0, .15);
        font-size: 14px;
        line-height: 20px;
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
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.top table td.title {
        font-size: 32px;
        line-height: 45px;
        color: #333;
    }
    
    .invoice-box table tr.information table td {
        padding-bottom: 20px;
    }
    
    .invoice-box table tr.heading td {
        background: #eee;
        text-align: right;
        border-bottom: 1px solid #ddd;
        border-right: 1px solid #ddd;
        font-weight: bold;
    }

    .invoice-box table tr.heading td:first-child {
        text-align: left;
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
        border-right: 2px solid #eee;
    }

    .invoice-box table tr.total td:nth-child(2) {
        border-right: 2px solid #eee;
        border-bottom: 2px solid #eee;
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

    .company {
        font-weight: bold;
        color: blue;
    }
	
    .company-address {
        font-weight: bold;
    }

	.label-box {
		display:inline-block;
		font-weight: bold;
		vertical-align:top;
		width: 100px;
        text-align: left;
	}
	
	.info-box {
		display:inline-block;
		width: 120px;
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
	
	.label-to {
		display:inline-block;
		background: #eee;
		font-weight: bold;
		border: 2px solid #eee;
		padding: 5px;
		width: 300px;
	}

	.label-to-contact {
		display:inline-block;
		font-weight: bold;
		border-left: 2px solid #eee;
		border-right: 2px solid #eee;
		padding: 5px;
		width: 300px;
	}

    .label-to-customer {
        font-weight: bold;
    }

	.label-to-address {
		display:inline-block;
		border: 2px solid #eee;
		padding: 5px;
		width: 300px;
	}
	
	.bottom-notes {
		display:inline-block;
		vertical-align:top;
		width: 330px;
	}
	
	.bottom-inter {
		display:inline-block;		
		width: 70px;
	}
	
	.bottom-sign {
		display:inline-block;		
		vertical-align:top;
		width: 220px;
		padding-top: 50px;
		font-weight: bold;
		border-bottom: 2px solid #000;
		text-align: center;
	}
	
	.bsign{
		text-align: center;
	}
    </style>
</head>

<body>
    <div class="invoice-box">
        <table cellpadding="0" cellspacing="0">
            <tr class="top">
                <td colspan="6">
                    <table>
                        <tr>
                            <td class="title">
                                <img src="<?php echo _ASSET_LOGO_FRONT; ?>" alt="<?php echo _COMPANY_NAME; ?>" />
                            </td>
                            <td class="title">
                                Purchase Order
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
                                <span class="company"><?=_COMPANY_NAME;?></span><br>
                                <span class="company-address">Ruko Prima Orchard Apartemen Blok C5<br/>
								Jl. Raya Perjuangan, Bekasi 17121 Jawa Barat<br/>
								Phone : (021)8889442<br/>
								NPWP  : 73.253.177.7-407.000</span>
                            </td>
                            <td>
								<br/>
                                <span class="label-box">Date</span><span class="inter">:</span><span class="info-box"><?=date('F j, Y',$rs->date_po);?></span><br/>
                                <span class="label-box">PO No</span><span class="inter">:</span><span class="info-box"><?=$rs->po;?></span><br/>
                                <span class="label-box">Page</span><span class="inter">:</span><span class="info-box">1 of 1</span><br/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr class="information">
                <td colspan="6">
                    <table>
                        <tr>
                            <td>
								<span class="label-to">To :</span><br/>
                                <span class="label-to-contact"><?=isset($rs->customer_name)?$rs->customer_name:'';?></span><br/>
                                <span class="label-to-address"><?=isset($rs->customer_address)?nl2br($rs->customer_address):'';?></span/>
                            </td>
                            <td>
								<br/>
                                <span class="label-box">Quotation No</span><span class="inter">:</span><span class="info-box"><?=isset($rs->quotation)?$rs->quotation:'';?></span><br/>
                                <span class="label-box">Currency</span><span class="inter">:</span><span class="info-box"><?=isset($rs->currency)?$rs->currency:'';?></span><br/>
                                <span class="label-box">Payment Term</span><span class="inter">:</span><span class="info-box"><?=isset($rs->term)?$rs->term:'';?></span><br/>
                                <span class="label-box">Due Date</span><span class="inter">:</span><span class="info-box"><?=date('F j, Y',$rs->date_due);?></span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>            
            <tr>
                <td colspan="6"><span class="label-quote">Purchase order for</span><span class="inter">:</span><span class="info-quote"><?=$rs->description;?></span></td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr class="heading">
                <td>No</td>
                <td style="width:300px;text-align:left;">Description</td>
                <td>Qty</td>
                <td>Unit</td>
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
                   Grand Total
                </td>
                <td>
                   <?=number_format($rs->worth,0,',','.');?>
                </td>
            </tr>
			<?php
			}
			?>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="6"></td>
            </tr>
            <tr>
                <td colspan="4">
					<span class="bottom-notes">Note :<br><?=nl2br($rs->notes);?></span>
					<span class="bottom-inter"></span>
				</td>
                <td colspan="2" class="bsign">
					<span class="bottom-sign"><?=_COMPANY_NAME;?><br/><br/><br/><br/><br/>Anung Wicaksono</span></td>
            </tr>
        </table>
    </div>
</body>
</html>