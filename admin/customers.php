<?php
session_start();

require_once('_helpers.php');
require_once('settings.php');

if(isset($_GET['format']) && $_GET['format']=='json'){
	header('Content-Type: application/json');
	
	Session::requireUser(true,ROLES::ADMIN);

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
Session::requireUser(false,ROLES::ADMIN);

require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30">
	<button type="button" class="btn btn-primary float-right mt5" data-toggle="modal" data-target="#modal-add-customer">Add new customer</button>
	<h1>Customers</h1>
</div>
<div id="data-customers" class="mt30"></div>
<div class="modal" id="modal-add-customer" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-add-customer">
				<div class="modal-header">
					<h5 class="modal-title">Add a new customer</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Name</label>
						<input type="text" name="name" class="form-control" placeholder="e.g., Fort Hays State University">
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Add customer</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
Template::set('footer/scripts','<script>getCustomers();</script>');
require_once(__DIR__ .'/theme/footer.php');


function getInsert(){
	require_once('lib/mysqlclass.php');
	if(!isset($_POST['name']{0})) die(json_encode(['status'=>'error','message'=>'Please enter all required fields']));
	$result=DB::get()->select('SELECT ID FROM customers WHERE name=?',[$_POST['name']]);
	if($result->rowCount()>0) die(json_encode(['status'=>'error','message'=>'The customer already exists']));
	$customer=['name'=>$_POST['name']];
	$customer_ID=DB::get()->insert('INSERT INTO customers('.implode(array_keys($customer),',').') VALUES('.implode(array_fill(0,count($customer),'?'),',').')',array_values($customer));
	die(json_encode(['status'=>'OK','message'=>'Record added','customer'=>['ID'=>$customer_ID,'n'=>$_POST['name']]]));
}

function getDelete($id){
	require_once('lib/mysqlclass.php');
	// TODO: verify if user can delete user
	DB::get()->insert('DELETE FROM customers WHERE ID=?',[$id]);
	DB::get()->insert('DELETE FROM customers_users WHERE customer_ID=?',[$id]);
	DB::get()->insert('DELETE FROM apps WHERE customer_ID=?',[$id]);
	// TODO: delete all the layers associated with the maps of this customer only
	// TODO: delete all the shapes associated with layers in the maps of this customer only
	die(json_encode(['status'=>'OK','message'=>'User has been deleted']));
}



function getIndex(){
	require_once('lib/mysqlclass.php');
	DB::get()->connect(Settings::get('db/host'),Settings::get('db/name'),Settings::get('db/user'),Settings::get('db/password'));
	$result=DB::get()->query('SELECT c.ID AS ID, c.name AS n FROM customers AS c ORDER BY ID ASC');
	die(json_encode(['status'=>'OK','customers'=>$result->fetchAll(),'count'=>$result->rowCount()]));
}