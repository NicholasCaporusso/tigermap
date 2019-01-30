<?php
session_start();

require_once('_helpers.php');
require_once('settings.php');

if(isset($_GET['action']) && $_GET['action']=='signout') Session::signOut();
elseif(isset($_GET['action']) && $_GET['action']=='signin' && isset($_GET['format']) && $_GET['format']=='json'){
	header('Content-Type: application/json');
	if(!isset($_POST['email']{0}) || !isset($_POST['password']{0}))  die(json_encode(['status'=>'error','message'=>'Authentication information missing']));	require_once('settings.php');
	
	$super=Settings::get('admin/super');
	foreach($super as $email=>$password) if($_POST['email']==$email && $_POST['password']==$password){
		$_SESSION['user/ID']=0;
		$_SESSION['user/role']=4;
		$_SESSION['user/name']='Super admin';
		$_SESSION['user/roles']=[];
		die(json_encode(['status'=>'OK','message'=>'User authenticated']));
	}
	
	require_once('lib/mysqlclass.php');
	$result=DB::get()->select('SELECT u.ID,u.password,u.role,cu.customer_ID,cu.role AS roles FROM users AS u LEFT JOIN customers_users AS cu ON u.ID=cu.user_ID WHERE email=?',[$_POST['email']]);
	if($result->rowCount()==0) die(json_encode(['status'=>'error','message'=>'User not found']));
	$user=[];
	$user['roles']=[];
	$roles_sum=0;
	while($row=$result->fetch()){
		$user['ID']=$row['ID'];
		$user['password']=$row['password'];
		$user['role']=$row['role'];
		$user['roles'][$row['customer_ID']]=$row['roles'];
		$roles_sum+=$row['roles'];
	}
	if(!password_verify($_POST['password'],$user['password'])) die(json_encode(['status'=>'error','message'=>'Wrong e-mail or password']));
	if($user['role']<1 && $roles_sum<1) die(json_encode(['status'=>'error','message'=>'You are not allowed to enter this area']));
	$_SESSION['user/ID']=$user['ID'];
	$_SESSION['user/role']=$user['role'];
	$_SESSION['user/roles']=$user['roles'];
	die(json_encode(['status'=>'OK','message'=>'User authenticated']));
}
if(isset($_SESSION['user/ID'])) header('location: users.php');

require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30"></div>
<h1>Access your account</h1>
<div id="alert" class="alert alert-danger alert-dismissible fade show" role="alert">
  <div class="alert-message"></div>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true">&times;</span>
  </button>
</div>
<form id="signinForm">
	<div class="form-group">
		<label for="exampleInputEmail1">Email address</label>
		<input name="email" type="email" class="form-control" placeholder="e.g., a_einstein@fhsu.edu">
	</div>
	<div class="form-group">
		<label for="exampleInputEmail1">Password</label>
		<input name="password" type="password" class="form-control" placeholder="e.g.,">
	</div>
	<button type="submit" class="btn btn-primary">Submit</button>
</form>
<?php
Template::set('footer/scripts','<script src="authenticate.js"></script>');
require_once(__DIR__ .'/theme/footer.php');