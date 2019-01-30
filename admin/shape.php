<?php
session_start();

require_once('_helpers.php');
require_once('settings.php');

if(isset($_GET['format']) && $_GET['format']=='json'){
	header('Content-Type: application/json');
	
	//Session::requireUser(true,ROLES::ADMIN);

	if(!isset($_GET['action'])) $_GET['action']='';
	switch($_GET['action']){
		case 'insert':
			getInsert();
		case 'delete':
			if(!isset($_GET['ID'])) die(json_encode(['status'=>'error','message'=>'Please, specify user ID']));
			getDelete($_GET['ID']);
			break;
		default: getIndex();
	}
}

function getIndex(){
	require_once('lib/mysqlclass.php');
	$data=isset($_GET['ID']) ? [$_GET['ID']] : [];
	$result=DB::get()->select('SELECT * FROM shapes WHERE ID=?',$data);
	die(json_encode(['status'=>'OK','shape'=>$result->fetch()]));
}


//Session::requireUser(false,ROLES::ADMIN);
require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30">
<a href="app.php?ID=" class="btn btn-secondary float-right mt5">Back to App</a>
<h1>Shape: <span id="customer_name"></span></h1>
</div>
<div class="card mt-4">
<img class="card-img-top img-fluid" src="http://placehold.it/900x400" alt="">
<div class="card-body" id="editor">
  
  <form>
  <div class="form-group">
    <label>Name</label>
    <input type="text" name="name" class="form-control" placeholder="name@example.com" value="{{name}}">
  </div>
  <div class="form-group">
    <label>Description</label>
    <textarea class="form-control" name="description" rows="3">{{description}}</textarea>
  </div>
</form>

</div>

<?php
Template::set('footer/scripts','<script src="shape.js"></script>');
require_once(__DIR__ .'/theme/footer.php');