/* GENERAL HELPERS */
function randomString(length=8) {
	var chars = "0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz";
	var randomstring = '';
	for (var i=0;i<length;i++) {
		var rnum = Math.floor(Math.random() * chars.length);
		randomstring += chars.substring(rnum,rnum+1);
	}
	return randomstring;
}
function getUrlVars() {
    var vars = {};
    var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
        vars[key] = value;
    });
    return vars;
}
var getVars=getUrlVars();

/* APP HELPERS */
function roleNumberToName(role){
	switch(role){
		case 0: return 'user';break;
		case 1: return 'author';break;
		case 2: return 'editor';break;
		case 3: return 'admin';break;
		case 4: return 'super';break;
	}
}

/* CUSTOMERS */
function getCustomers(){
	$.getJSON("customers.php?action=index&format=json",function(data){
		var template='<table class="table table-striped"><thead><tr><th scope="col">ID</th><th scope="col">Name</th><th scope="col">Actions</th></tr></thead>{{#customers}}<tr><td>{{ID}}</td><td>{{n}}</td><td><a href="customer.php?ID={{ID}}" class="btn btn-outline-dark btn-xs" data-id="{{ID}}">edit</a> <button type="button" class="btn btn-outline-danger btn-delete-customer btn-xs" data-id="{{ID}}">delete</button> </td></tr>{{/customers}}</table>';
		Mustache.parse(template);
		var rendered=Mustache.render(template,data);
		$('#data-customers').html(rendered);
	});
}
$(document).on('click','.btn-delete-customer',function(){
	if(!confirm("You are about to delete a customer and all its related records. Are you sure?")) return;
	var object=$(this);
	$.getJSON("customers.php?action=delete&format=json&ID="+object.data('id'),function(data){
		if(data.status=='OK') object.parent().parent().remove();
	});
});
$('#modal-add-customer').on('submit',function(e){
	e.preventDefault();
	var id=$(this).attr('id');
	if($('#'+id+' input[name="name"]').val().length==0){
		$('#alert').removeClass('alert-success').addClass('alert-danger');
		$('#alert .alert-message').text('Please, enter your e-mail and password');
		$('#alert').show();
		return;
	}
	$.post("customers.php?action=insert&format=json",
		{
			name:$('#'+id+' input[name="name"]').val()
		},
		function(data){
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
					$('#modal-add-customer').modal('hide');
					$('#'+id+' input[name="name"]').val('');
					$('#data-customers table').append('<tr><td>'+ data.customer.ID +'</td><td>'+ data.customer.n +'</td><td><button type="button" class="btn btn-outline-dark btn-xs" data-id="'+ data.customer.ID +'">edit</button> <button type="button" class="btn btn-outline-danger btn-delete-customer btn-xs" data-id="'+ data.customer.ID +'">delete</button> </td></tr>');
					break;
			}
	});
});

/* USERS */
function getUsers(){
	$.getJSON("users.php?action=index&format=json"+(getVars['cID']!=undefined ? '&cID='+getVars['cID']: ''),function(data){
		data.cRole=function(){
			return roleNumberToName(this.cR);
		}
		var template='<table class="table table-striped"><thead><tr><th scope="col">ID</th><th scope="col">E-mail</th><th scope="col">Name</th><th scope="col">Status</th><th scope="col">Customers</th><th scope="col">Actions</th></tr></thead>{{#users}}<tr><td>{{ID}}</td><td>{{e}}</td><td>{{f}} {{l}}</td><td>{{s}}</td><td>{{#cs}}{{cRole}} at {{cN}} ({{cID}}) {{/cs}}</td><td><button type="button" class="btn btn-outline-dark btn-xs" data-id="{{ID}}">edit</button> <button type="button" class="btn btn-outline-danger btn-user-delete btn-xs" data-id="{{ID}}">delete</button> </td></tr>{{/users}}</table>';
		Mustache.parse(template);
		var rendered=Mustache.render(template,data);
		$('#data-users').html(rendered);
		
		template='{{#customers}}<option value="{{ID}}">{{n}}</option>{{/customers}}';
		Mustache.parse(template);
		var rendered=Mustache.render(template,data);
		$('#select-users-customer_ID').html(rendered);
		$('#form-user-add select[name="role"]').prop('selectedIndex', 0);
		$('#form-user-add select[name="customer_ID"]').prop('selectedIndex', 0);
	});
}
$(document).on('click','.btn-user-delete',function(){
	var object=$(this);
	$.getJSON("users.php?action=delete&format=json&ID="+object.data('id'),function(data){
		if(data.status=='OK') object.parent().parent().remove();
	});
});

$('.btn-user-add').on('click',function(){
	$('#form-user-add input[name="password"]').val(randomString(10));
	if($(this).data('type')=='admin') $('#formgroup-users-customers').hide();
	else $('#formgroup-users-customers').show();
	$('#modal-user-add').modal('show');
});

$('#form-user-add').on('submit',function(e){
	e.preventDefault();
	var id=$(this).attr('id');
	if($('#'+id+' input[name="email"]').val().length==0){
		$('#alert').removeClass('alert-success').addClass('alert-danger');
		$('#alert .alert-message').text('Please, enter your e-mail and password');
		$('#alert').show();
		return;
	}
	$.post("users.php?action=insert&format=json",{
			email:$('#'+id+' input[name="email"]').val(),
			password:$('#'+id+' input[name="password"]').val(),
			firstname:$('#'+id+' input[name="firstname"]').val(),
			lastname:$('#'+id+' input[name="lastname"]').val(),
			role:$('#'+id+' select[name="role"]').val(),
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
				$('#alert').hide();
				$('#modal-user-add').modal('hide');
				$('#'+id+' input[name="email"]').val('');
				$('#'+id+' input[name="firstname"]').val('');
				$('#'+id+' input[name="password"]').val('');
				$('#'+id+' input[name="lastname"]').val('');
				$('#'+id+' select[name="role"]').prop('selectedIndex', 0);
				$('#'+id+' select[name="customer_ID"]').prop('selectedIndex', 0);
				var customer='';
				if(data.user.cs!=undefined) customer=roleNumberToName(data.user.cs[0].cR)+' at '+data.user.cs[0].cN+' ('+data.user.cs[0].cID+')';
				$('#data-users table').append('<tr><td>'+data.user.ID+'</td><td>'+data.user.e+'</td><td>'+data.user.n+'</td><td>'+data.user.s+'</td><td>'+customer+'</td><td><button type="button" class="btn btn-outline-dark btn-xs" data-id="'+data.user.ID+'">edit</button> <button type="button" class="btn btn-outline-danger btn-user-delete btn-xs" data-id="'+data.user.ID+'">delete</button> </td></tr>');
				break;
		}
	});
});

/* APPS */