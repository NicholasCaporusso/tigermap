<?php
session_start();
require_once('_helpers.php');
require_once('settings.php');

if(isset($_GET['format']) && $_GET['format']=='json'){
	header('Content-Type: application/json');
	
	Session::requireUser(true,ROLES::EDITOR,ROLES::EDITOR);

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
Session::requireUser(false,ROLES::EDITOR,ROLES::EDITOR);

require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30">
	<?php 
	if(Session::isUser(ROLES::EDITOR,ROLES::EDITOR)) echo '<button type="button" class="btn btn-primary float-right mt5" data-toggle="modal" data-target="#modal-app-add">Add new app</button>';
	?>
	<h1>Apps</h1>
</div>
<div id="data-apps" class="mt30"></div>
<div class="modal" id="modal-app-add" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document">
		<div class="modal-content">
			<form id="form-app-add">
				<div class="modal-header">
					<h5 class="modal-title">Add a new app</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label>Name</label>
						<input type="name" name="name" class="form-control" placeholder="e.g., My app project">
					</div>
					<div class="form-group">
						<label>Customer</label>
						<select class="form-control" id="select-app-customer_ID" name="customer_ID">
						</select>
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Add app</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
Template::set('footer/scripts','<script src="apps.js"></script><script>getApplications()</script>');
require_once(__DIR__ .'/theme/footer.php');

function getInsert(){
	require_once('lib/mysqlclass.php');
	if(!isset($_POST['name']{0}) || !isset($_POST['customer_ID'])) die(json_encode(['status'=>'error','message'=>'Please enter all required fields']));
	$result=DB::get()->select('SELECT ID FROM apps WHERE name=? AND customer_ID=?',[$_POST['name'],$_POST['customer_ID']]);
	if($result->rowCount()>0) die(json_encode(['status'=>'error','message'=>'An app with that name already exists']));
	$user=['name'=>$_POST['name'],'customer_ID'=>$_POST['customer_ID']];
	$app_ID=DB::get()->insert('INSERT INTO apps('.implode(array_keys($user),',').') VALUES('.implode(array_fill(0,count($user),'?'),',').')',array_values($user));
	die(json_encode(['status'=>'OK','message'=>'The app has been created','app'=>['ID'=>$app_ID,'n'=>$_POST['name'],'cID'=>$_POST['customer_ID'],'cN'=>DB::get()->select('SELECT name FROM customers WHERE ID=?',[$_POST['customer_ID']])->fetchColumn()]]));
}
function getDelete($id){
	require_once('lib/mysqlclass.php');
	// TODO: verify if user can delete user
	// TODO: delete user
	// TODO: delete customers_apps
	die(json_encode(['status'=>'OK','message'=>'User has been deleted']));
}
function getIndex(){
	require_once('lib/mysqlclass.php');
	$data=isset($_GET['cID']) ? [$_GET['cID']] : [];
	if($_SESSION['user/role']>Roles::USER){
		$apps=DB::get()->select('SELECT a.ID AS ID, a.name AS n,a.customer_ID AS cID, c.name as cN FROM apps AS a JOIN customers AS c ON a.customer_ID=c.ID '.(isset($_GET['cID']) ? ' WHERE c.ID=?' : '').' ORDER BY a.ID ASC',$data);
		$customers=DB::get()->query('SELECT c.ID AS ID,c.name AS n FROM customers AS c ORDER BY c.ID ASC');
	}
	else{
		$apps=DB::get()->select('SELECT a.ID AS ID, a.name AS n,a.customer_ID AS cID, c.name as cN FROM apps AS a JOIN customers AS c ON a.customer_ID=c.ID '.(isset($_GET['cID']) ? ' WHERE c.ID=?' : ' WHERE c.ID IN('.implode(array_keys($_SESSION['user/roles']),',').')').' ORDER BY a.ID ASC',$data);
		$customers=DB::get()->query('SELECT c.ID AS ID,c.name AS n FROM customers AS c WHERE c.ID IN('.implode(array_keys($_SESSION['user/roles']),',').') ORDER BY c.ID ASC');
	}
	if(isset($customers)) $user['customers']=$customers;
	die(json_encode(['status'=>'OK','apps'=>$apps->fetchAll(),'count'=>$apps->rowCount(),'customers'=>$customers->fetchAll()]));
}