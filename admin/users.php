<?php
session_start();

require_once('_helpers.php');
require_once('settings.php');

if(isset($_GET['format']) && $_GET['format']=='json'){
	header('Content-Type: application/json');
	
	Session::requireUser(true,ROLES::ADMIN,ROLES::ADMIN);

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
Session::requireUser(false,ROLES::ADMIN,ROLES::ADMIN);

require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30"> 
	<?php if($_SESSION['user/role']>=Roles::ADMIN){ ?>
	<div class="btn-group float-right">
		<button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Add new user</button>
		<div class="dropdown-menu">
			<button class="dropdown-item btn-user-add" href="#">Standard user</button>
			<button class="dropdown-item btn-user-add" data-type="admin">Admin</button>
		</div>
	</div>
	<?php }else{ ?> 
	<button type="button" class="btn btn-primary float-right mt5 btn-user-add">Add new user</button>
	<?php } ?> 
	<h1>Users</h1>
</div>
<div id="data-users" class="mt30"></div>
<div class="modal" id="modal-user-add" tabindex="-1" role="dialog" aria-hidden="true">
	<div class="modal-dialog" role="document" style="max-width:800px">
		<div class="modal-content">
			<form id="form-user-add">
				<div class="modal-header">
					<h5 class="modal-title">Add a new user</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div id="alert" class="alert alert-danger alert-dismissible fade show" role="alert">
						<div class="alert-message"></div>
						<button type="button" class="close" data-dismiss="alert" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>Email address</label>
								<input type="email" name="email" class="form-control" placeholder="name@example.com">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Password</label>
								<input type="text" name="password" class="form-control" placeholder="name@example.com">
							</div>
						</div>
					</div>
					<div class="row">
						<div class="col-lg-6">
							<div class="form-group">
								<label>First name</label>
								<input type="text" name="firstname" class="form-control" placeholder="name@example.com">
							</div>
						</div>
						<div class="col-lg-6">
							<div class="form-group">
								<label>Last name</label>
								<input type="text" name="lastname" class="form-control" placeholder="name@example.com">
							</div>
						</div>
					</div>
					<div class="form-group">
						<label>Role</label>
						<select class="form-control" name="role">
							<option value="<?= ROLES::USER ?>">User</option>
							<option value="<?= ROLES::AUTHOR ?>">Author</option>
							<option value="<?= ROLES::EDITOR ?>">Editor</option>
							<option value="<?= ROLES::ADMIN ?>">Admin</option>
							<option value="<?= ROLES::SUPER ?>">Super</option>
						</select>
					</div>
					<?php if($_SESSION['user/role']>=Roles::ADMIN){ ?>
					<div id="formgroup-users-customers" class="form-group">
						<label for="selectCustomers">Customer(s)</label>
						<select class="form-control" id="select-users-customer_ID" name="customer_ID">
						</select>
					</div>
					<?php } ?>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
					<button type="submit" class="btn btn-primary">Add user</button>
				</div>
			</div>
		</form>
	</div>
</div>
<?php
Template::set('footer/scripts','<script>getUsers();</script>');
require_once(__DIR__ .'/theme/footer.php');

function getInsert(){
	require_once('lib/mysqlclass.php');
	if(!isset($_POST['email']{0})) die(json_encode(['status'=>'error','message'=>'Please enter all required fields']));
	$result=DB::get()->select('SELECT ID FROM users WHERE email=?',[$_POST['email']]);
	if($result->rowCount()>0) die(json_encode(['status'=>'error','message'=>'The user already exists']));
	$user=['email'=>$_POST['email'],'firstname'=>$_POST['firstname'],'lastname'=>$_POST['lastname'],'password'=>password_hash($_POST['password'],PASSWORD_DEFAULT),'status'=>0];
	$user['role']= isset($_POST['customer_ID']) && is_numeric($_POST['customer_ID']) ? 0 : $_POST['role'];
	$user_ID=DB::get()->insert('INSERT INTO users('.implode(array_keys($user),',').') VALUES('.implode(array_fill(0,count($user),'?'),',').')',array_values($user));
	if(isset($_POST['customer_ID']) && is_numeric($_POST['customer_ID'])){
		DB::get()->insert('INSERT INTO customers_users(customer_ID,user_ID,role) VALUES(?,?,?)',[$_POST['customer_ID'],$user_ID,$_POST['role']]);
		die(json_encode(['status'=>'OK','message'=>'User has been added','user'=>[
		'ID'=>$user_ID,'e'=>$user['email'],'n'=>$user['firstname'].' '.$user['lastname'],'s'=>$user['status'],'cs'=>[['cID'=>$_POST['customer_ID'],'cR'=>$_POST['role'],'cN'=>DB::get()->select('SELECT name FROM customers WHERE ID=?',[$_POST['customer_ID']])->fetchColumn()]]]]));
	}else die(json_encode(['status'=>'OK','message'=>'User has been added','user'=>[
		'ID'=>$user_ID,'e'=>$user['email'],'n'=>$user['firstname'].' '.$user['lastname'],'s'=>$user['status']]]));
}
function getDelete($id){
	require_once('lib/mysqlclass.php');
	// TODO: verify if user can delete user
	// TODO: delete user
	// TODO: delete customers_users
	die(json_encode(['status'=>'OK','message'=>'User has been deleted']));
}

function getIndex(){
	require_once('lib/mysqlclass.php');
	$data=isset($_GET['cID']) ? [$_GET['cID']] : [];
	$result=DB::get()->select('SELECT u.ID AS ID,u.email AS e,u.firstname AS f,u.lastname AS l,u.status AS s,c.ID AS cID,c.name AS cN,cu.role AS cR FROM users AS u '.(isset($_GET['cID']) ? '' : 'LEFT').' JOIN customers_users AS cu ON cu.user_ID=u.ID '.(isset($_GET['cID']) ? '' : 'LEFT').' JOIN customers AS c ON cu.customer_ID=c.ID '.(isset($_GET['cID']) ? ' WHERE c.ID=?' : '').' ORDER BY u.ID ASC',$data);
	$users=[];
	while($row=$result->fetch()){
		$customers=is_numeric($row['cID']) ? ['cID'=>$row['cID'],'cN'=>$row['cN'],'cR'=>$row['cR']] : [];
		$row=array_diff_key($row,['cID'=>'','cN'=>'','cR'=>'']);
		$users[$row['ID']]=$row;
		if(count($customers)>0) $users[$row['ID']]['cs'][]=$customers;
	}
	$customers=DB::get()->query('SELECT c.ID AS ID,c.name AS n FROM customers AS c ORDER BY c.ID ASC');
	if(isset($customers)) $user['customers']=$customers;
	die(json_encode(['status'=>'OK','users'=>array_values($users),'count'=>count($users),'customers'=>$customers->fetchAll()]));
}