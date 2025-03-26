		var Login = function() {

			var handleLogin = function() {

				$('.login-form').validate({
					errorElement: 'span', //default input error message container
					errorClass: 'help-block', // default input error message class
					focusInvalid: false, // do not focus the last invalid input
					rules: {
						username: {
							required: true
						},
						userpasswd: {
							required: true
						},
						remember_me: {
							required: false
						}
					},

					messages: {
						username: {
							required: "Username is required."
						},
						userpasswd: {
							required: "Password is required."
						}
					},

					invalidHandler: function(event, validator) { //display error alert on form submit 
						$('.alert-danger', $('.login-form')).html('<button class="close" data-close="alert"></button><span>Enter your username and password.</span>');
						$('.alert-danger', $('.login-form')).show();
					},

					highlight: function(element) { // hightlight error inputs
						$(element)
							.closest('.form-group').addClass('has-error'); // set error class to the control group
					},

					success: function(label) {
						label.closest('.form-group').removeClass('has-error');
						label.remove();
					},

					errorPlacement: function(error, element) {
						error.insertAfter(element.closest('.input-icon'));
					},

					submitHandler: function(form) {
						$('.alert-danger', $('.login-form')).hide();
						$.ajax({
							url : 'login/auth',
							data : $('#login-form').serialize(),
							type: "POST",
							success : function(data){
								if(data=='welcome'){
									window.location.href = '/dashboard';
								} else {
									$('.alert-danger', $('.login-form')).html(data);
									$('.alert-danger', $('.login-form')).show();
								}
							}
						})
						return false;
						//form.submit(); // form validation success, call ajax form submit
					}
				});

				$('.login-form input').keypress(function(e) {
					if (e.which == 13) {
						if ($('.login-form').validate().form()) {
							$('.login-form').submit(); //form validation success, call ajax form submit
						}
						return false;
					}
				});

				$('.forget-form input').keypress(function(e) {
					if (e.which == 13) {
						if ($('.forget-form').validate().form()) {
							$('.forget-form').submit();
						}
						return false;
					}
				});

				$('#forget-password').click(function(){
					$('.login-form').hide();
					$('.forget-form').show();
				});

				$('#back-btn').click(function(){
					$('.login-form').show();
					$('.forget-form').hide();
				});
			}

		 	return {
				//main function to initiate the module
				init: function() {

					handleLogin();
					var path= bPath();

					// init background slide images
					$('.login-bg').backstretch([
						path+"login/bg1.jpg",
						path+"login/bg2.jpg",
						path+"login/bg3.jpg"
						], {
						  fade: 1000,
						  duration: 8000
						}
					);

					$('.forget-form').hide();

				}

			};

		}();

		jQuery(document).ready(function() {
			Login.init();
		});