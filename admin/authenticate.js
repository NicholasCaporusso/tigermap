$('#signinForm').on('submit',function(e){
	e.preventDefault();
	if($('#signinForm input[name="email"]').val().length==0 || $('#signinForm input[name="password"]').val().length==0){
		$('#alert').removeClass('alert-success').addClass('alert-danger');
		$('#alert .alert-message').text('Please, enter your e-mail and password');
		$('#alert').show();
		return;
	}
	$.ajax({
		method:"POST",
		url:"authenticate.php?action=signin&format=json",
		data:{
			email:$('#signinForm input[name="email"]').val(),
			password:$('#signinForm input[name="password"]').val()
		}
	}).done(function(msg){
		switch(msg.status){
			case 'error':
				$('#alert').removeClass('alert-success').addClass('alert-danger');
				$('#alert .alert-message').text(msg.message);
				$('#alert').show();
				break;
			case 'OK':
				$('#alert').removeClass('alert-danger').addClass('alert-success');
				$('#alert .alert-message').text(msg.message);
				$('#alert').show();
				window.location.href='index.php';
				break;
		}
	});
});
