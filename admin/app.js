$.getJSON("app.php?action=index&format=json"+(getVars['ID']!=undefined ? '&ID='+getVars['ID']: ''),function(data){
	data.type=function(){
		switch(this.t){
			case 0: return 'marker';
			case 1: return 'poi';
			case 2: return 'circle';
			case 3: return 'polyline';
			case 4: return 'polygon';
			case 5: return 'parking';
			case 6: return 'building';
		}
	}
	var template='<table class="table table-striped"><thead><tr><th scope="col">ID</th><th scope="col">Type</th><th scope="col">Name</th><th scope="col">Actions</th></tr></thead>{{#shapes}}<tr><td>{{ID}}</td><td>{{type}}</td><td>{{n}}</td><td><a class="btn btn-outline-dark btn-xs" href="shape.php?ID={{ID}}&appID='+getVars['ID']+'">edit</a> <button type="button" class="btn btn-outline-danger btn-xs" data-id="{{ID}}">delete</button> </td></tr>{{/shapes}}</table>';
	Mustache.parse(template);
	var rendered=Mustache.render(template,data);
	$('#shapes_data').html(rendered);
	
	template='{{#customers}}<option value="{{ID}}">{{n}}</option>{{/customers}}';
	Mustache.parse(template);
	var rendered=Mustache.render(template,data);
	$('#selectCustomers').html(rendered);
});

$(document).on('click','.btn-outline-danger',function(){
	var object=$(this);
	$.getJSON("apps.php?action=delete&format=json&ID="+object.data('id'),function(data){
		if(data.status=='OK') object.parent().parent().remove();
	});
});

$('#addForm').on('submit',function(e){
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
				$('#addModal').modal('hide');
				$('#'+id+' input[name="name"]').val('');
				$('#'+id+' input[name="customer_ID"]').val('');
				$('#data').append('<tr><td>{{ID}}</td><td>{{n}}</td><td><button type="button" class="btn btn-outline-dark btn-xs" data-id="{{ID}}">edit</button> <button type="button" class="btn btn-outline-danger btn-xs" data-id="{{ID}}">delete</button> </td></tr>');
				/* TODO: add row to table */
				break;
		}
	});
});