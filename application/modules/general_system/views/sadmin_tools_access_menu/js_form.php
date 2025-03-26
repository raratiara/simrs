<script type="text/javascript">
var FormValidation = function () {
    var handleValidation = function() {
		var form = $('#frmData');
		var error = $('.alert-danger', form);
		var success = $('.alert-success', form);

		form.validate({
			errorElement: 'span', //default input error message container
			errorClass: 'help-block help-block-error', // default input error message class
			focusInvalid: false, // do not focus the last invalid input
			ignore: "", // validate all fields including form hidden input
			rules: {
				user_id: {
					required: true
				}
			},
			messages: { // custom messages for radio buttons and checkboxes
			},
			errorPlacement: function (error, element) { // render error placement for each input type
				if (element.parent(".input-group").size() > 0) {
					error.insertAfter(element.parent(".input-group"));
				} else if (element.attr("data-error-container")) { 
					error.appendTo(element.attr("data-error-container"));
				} else if (element.parents('.radio-list').size() > 0) { 
					error.appendTo(element.parents('.radio-list').attr("data-error-container"));
				} else if (element.parents('.radio-inline').size() > 0) { 
					error.appendTo(element.parents('.radio-inline').attr("data-error-container"));
				} else if (element.parents('.checkbox-list').size() > 0) {
					error.appendTo(element.parents('.checkbox-list').attr("data-error-container"));
				} else if (element.parents('.checkbox-inline').size() > 0) { 
					error.appendTo(element.parents('.checkbox-inline').attr("data-error-container"));
				} else {
					error.insertAfter(element); // for other inputs, just perform default behavior
				}
			},
			invalidHandler: function (event, validator) { //display error alert on form submit   
				success.hide();
				error.show();
				App.scrollTo(error, -200);
			},
			highlight: function (element) { // hightlight error inputs
				$(element)
					.closest('.form-group').addClass('has-error'); // set error class to the control group
			},
			unhighlight: function (element) { // revert the change done by hightlight
				$(element)
					.closest('.form-group').removeClass('has-error'); // set error class to the control group
			},
			success: function (label) {
				label
					.closest('.form-group').removeClass('has-error'); // set success class to the control group
			},
			submitHandler: function (form) {
				success.show();
				error.hide();
				form[0].submit(); // submit the form
			}
		});

		//apply validation on select2 dropdown value change, this only needed for chosen dropdown integration.
		$('.select2me', form).change(function () {
			form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
		});

		//initialize datepicker
		$('.date-picker').datepicker({
			rtl: App.isRTL(),
			autoclose: true
		});
		$('.date-picker .form-control').change(function() {
			form.validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input 
		})
    }

    return {
        init: function () {
            handleValidation();
        }
    };
}();
	
jQuery(document).ready(function() {
    FormValidation.init();
});
			jQuery(function($) {  
				$("#view").click(function() {  
					if(this.checked){
			            $('.checkbox_view').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_view').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_view').on('click',function(){
			        if($('.checkbox_view:checked').length == $('.checkbox_view').length){
			            $('#view').prop('checked',true);
			        }else{
			            $('#view').prop('checked',false);
			        }
					$.uniform.update();
			    }); 

				$("#add").click(function() {  
					if(this.checked){
			            $('.checkbox_add').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_add').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_add').on('click',function(){
			        if($('.checkbox_add:checked').length == $('.checkbox_add').length){
			            $('#add').prop('checked',true);
			        }else{
			            $('#add').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$("#update").click(function() {  
					if(this.checked){
			            $('.checkbox_update').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_update').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_update').on('click',function(){
			        if($('.checkbox_update:checked').length == $('.checkbox_update').length){
			            $('#update').prop('checked',true);
			        }else{
			            $('#update').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$("#delete").click(function() {  
					if(this.checked){
			            $('.checkbox_delete').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_delete').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_delete').on('click',function(){
			        if($('.checkbox_delete:checked').length == $('.checkbox_delete').length){
			            $('#delete').prop('checked',true);
			        }else{
			            $('#delete').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$("#detail").click(function() {  
					if(this.checked){
			            $('.checkbox_detail').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_detail').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_detail').on('click',function(){
			        if($('.checkbox_detail:checked').length == $('.checkbox_detail').length){
			            $('#detail').prop('checked',true);
			        }else{
			            $('#detail').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$("#import").click(function() {  
					if(this.checked){
			            $('.checkbox_import').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_import').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_import').on('click',function(){
			        if($('.checkbox_import:checked').length == $('.checkbox_import').length){
			            $('#import').prop('checked',true);
			        }else{
			            $('#import').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$("#eksport").click(function() {  
					if(this.checked){
			            $('.checkbox_eksport').each(function(){
			                this.checked = true;
			            });
			        }else{
			             $('.checkbox_eksport').each(function(){
			                this.checked = false;
			            });
			        }
					$.uniform.update();
				});
				$('.checkbox_eksport').on('click',function(){
			        if($('.checkbox_eksport:checked').length == $('.checkbox_eksport').length){
			            $('#eksport').prop('checked',true);
			        }else{
			            $('#eksport').prop('checked',false);
			        }
					$.uniform.update();
			    });

				$( "#SubmitData" ).click(function() {  
				  	$("#frmData" ).submit();
				});  
			})
</script>
