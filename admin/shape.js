$.getJSON("shape.php?action=index&format=json"+(getVars['ID']!=undefined ? '&ID='+getVars['ID']: ''),function(data){
	var template;
	switch(data.shape.type){
		case 4: template='<div class="form-group">    <label>Name</label>    <input type="text" name="name" class="form-control" placeholder="name@example.com" value="{{name}}">  </div>  <div class="form-group">    <label>Description</label>    <textarea class="form-control" name="description" rows="3">{{description}}</textarea>  </div>';
		console.log('x');
		break;
	}
	Mustache.parse(template);
	var rendered=Mustache.render(template,data.shape);
	$('#editor').html(rendered);
});
$(document).on('click','.btn-outline-danger',function(){
	if(!confirm("You are about to delete a customer and all its related records. Are you sure?")) return;
	var object=$(this);
	$.getJSON("customers.php?action=delete&format=json&ID="+object.data('id'),function(data){
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
	$.post("customers.php?action=insert&format=json",{
			name:$('#'+id+' input[name="name"]').val()
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
				$('#data').append('<tr><td>{{ID}}</td><td>{{n}}</td><td><button type="button" class="btn btn-outline-dark btn-xs" data-id="{{ID}}">edit</button> <button type="button" class="btn btn-outline-danger btn-xs" data-id="{{ID}}">delete</button> </td></tr>');
				/* TODO: add row to table */
				break;
		}
	});
});