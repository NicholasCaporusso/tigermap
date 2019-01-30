function getApplications(){
	$.getJSON("apps.php?action=index&format=json"+(getVars['cID']!=undefined ? '&cID='+getVars['cID']: ''),function(data){
		var template='<table class="table table-striped"><thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Customers</th><th scope="col">Actions</th></tr></thead>{{#apps}}<tr><td>{{ID}}</td><td>{{n}}</td><td>{{cID}} {{cN}}</td><td><a class="btn btn-outline-dark btn-xs" href="app.php?ID={{ID}}">edit</a> <button type="button" class="btn btn-outline-danger btn-app-delete btn-xs" data-id="{{ID}}">delete</button> </td></tr>{{/apps}}</table>';
		Mustache.parse(template);
		var rendered=Mustache.render(template,data);
		$('#data-apps').html(rendered);
		
		template='{{#customers}}<option value="{{ID}}">{{n}}</option>{{/customers}}';
		Mustache.parse(template);
		var rendered=Mustache.render(template,data);
		$('#select-app-customer_ID').html(rendered);
	});
}
$(document).on('click','.btn-outline-danger .btn-app-delete',function(){
	var object=$(this);
	$.getJSON("apps.php?action=delete&format=json&ID="+object.data('id'),function(data){
		if(data.status=='OK') object.parent().parent().remove();
	});
});
$('#form-app-add').on('submit',function(e){
	e.preventDefault();
	var id=$(this).attr('id');
	if($('#'+id+' input[name="name"]').val().length==0){
		$('#alert').removeClass('alert-success').addClass('alert-danger');
		$('#alert .alert-message').text('Please, enter your e-mail and password');
		$('#alert').show();
		return;
	}
	$.post("apps.php?action=insert&format=json",{
			name:$('#'+id+' input[name="name"]').val(),
			customer_ID:$('#'+id+' select[name="customer_ID"]').val()
		},function(data){
		switch(data.status){
			case 'error':
				$('#alert').removeClass('alert-success').addClass('alert-danger');
				$('#alert .alert-message').text(data.message);
				$('#alert').show();
				break;
			case 'OK':
				$('#alert').removeClass('alert-danger').addClass('alert-success');
				$('#alert .alert-message').text(data.message);
				$('#alert').show();
				$('#modal-app-add').modal('hide');
				$('#'+id+' input[name="name"]').val('');
				$('#'+id+' input[name="customer_ID"]').val('');
				$('#data-apps table').append('<tr><td>'+data.app.ID+'</td><td>'+data.app.n+'</td><td>'+data.app.cID+' '+data.app.cN+'</td><td><a class="btn btn-outline-dark btn-xs" href="app.php?ID='+data.app.ID+'">edit</a> <button type="button" class="btn btn-outline-danger btn-app-delete btn-xs" data-id="{{'+data.app.ID+'}}">delete</button> </td></tr>');
				break;
		}
	});
});