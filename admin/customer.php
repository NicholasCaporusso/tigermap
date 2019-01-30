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
	$result=DB::get()->select('SELECT s.ID AS ID,s.type AS t,s.name AS n,s.description AS d,s.details AS i FROM shapes AS s JOIN apps_shapes AS sa ON sa.shape_ID=s.ID WHERE sa.app_ID=?',$data);
	die(json_encode(['status'=>'OK','shapes'=>$result->fetchAll(),'count'=>$result->rowCount()]));
}


//Session::requireUser(false,ROLES::ADMIN);
require_once(__DIR__ .'/theme/header.php');
?>
<div class="mt30">
	<a href="customers.php" class="btn btn-secondary float-right mt5">Back to Customers</a>
	<h1>Customer: <span id="customer_name"></span></h1>
</div>
<ul class="nav nav-tabs mt30" id="myTab" role="tablist">
	<li class="nav-item">
		<a class="nav-link active" data-toggle="tab" href="#tab-customer-overview" role="tab" aria-controls="overview" aria-selected="true">Overview</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#tab-customer-users" role="tab" aria-controls="users" aria-selected="false">Users</a>
	</li>
	<li class="nav-item">
		<a class="nav-link" data-toggle="tab" href="#tab-customer-apps" role="tab" aria-controls="apps" aria-selected="false">Apps</a>
	</li>
</ul>
<div class="tab-content" id="myTabContent">
	<div class="tab-pane show active" id="tab-customer-overview" role="tabpanel" aria-labelledby="overview-tab">
		<div class="row">
			<div class="col-lg-3">
				<div class="list-group my-4">
					<a href="#" class="list-group-item active">Category 1</a>
					<a href="#" class="list-group-item">Category 2</a>
					<a href="#" class="list-group-item">Category 3</a>
				</div>
			</div>
			<div class="col-lg-9">

				<div class="card mt-4">
					<img class="card-img-top img-fluid" src="http://placehold.it/900x400" alt="">
					<div class="card-body">
						<h3 class="card-title">Product Name</h3>
						<h4>$24.99</h4>
						<p class="card-text">Lorem ipsum dolor sit amet, consectetur adipisicing elit. Sapiente dicta fugit fugiat hic aliquam itaque facere, soluta. Totam id dolores, sint aperiam sequi pariatur praesentium animi perspiciatis molestias iure, ducimus!</p>
						<span class="text-warning">&#9733; &#9733; &#9733; &#9733; &#9734;</span>
						4.0 stars
					</div>
				</div>
				<!-- /.card -->

				<div class="card card-outline-secondary my-4">
					<div class="card-header">
						Product Reviews
					</div>
					<div class="card-body">
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
						<small class="text-muted">Posted by Anonymous on 3/1/17</small>
						<hr>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
						<small class="text-muted">Posted by Anonymous on 3/1/17</small>
						<hr>
						<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit. Omnis et enim aperiam inventore, similique necessitatibus neque non! Doloribus, modi sapiente laboriosam aperiam fugiat laborum. Sequi mollitia, necessitatibus quae sint natus.</p>
						<small class="text-muted">Posted by Anonymous on 3/1/17</small>
						<hr>
						<a href="#" class="btn btn-success">Leave a Review</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="tab-pane fade" id="tab-customer-users" role="tabpanel" aria-labelledby="shapes-tab">
		<div id="shapes_data" class="mt30"></div>
	</div>
	<div class="tab-pane fade" id="tab-customer-apps" role="tabpanel" aria-labelledby="contact-tab">
		<div id="data" class="mt30"></div>
	</div>
</div>
<?php
Template::set('footer/scripts','<script src="app.js"></script>');
require_once(__DIR__ .'/theme/footer.php');