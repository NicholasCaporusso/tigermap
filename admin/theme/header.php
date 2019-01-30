<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title><?= Template::get('title') ?></title>

    <!-- Bootstrap core CSS -->
    <link href="theme/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="theme/css/3-col-portfolio.css" rel="stylesheet">
	<style>
	#alert{display:none}
	.btn-xs{
		padding: 1px 5px;
		font-size: 80%;
	}
	.mt30{margin-top:30px}
	.mt5{margin-top:5px}
	</style>
  </head>

  <body>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
      <div class="container">
        <a class="navbar-brand" href="index.php">Tiger map</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarResponsive" aria-controls="navbarResponsive" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarResponsive">
          <ul class="navbar-nav ml-auto">
			<?php if(isset($_SESSION['user/ID'])){ 
				if($_SESSION['user/role']>Roles::EDITOR){
					echo '<li class="nav-item"><a class="nav-link" href="customers.php">Customers</a></li>';
					echo '<li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>';
				}
			?>
            <li class="nav-item">
              <a class="nav-link" href="apps.php">Apps</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="authenticate.php?action=signout">Signout</a>
            </li>
			<?php }else{ ?>
            <li class="nav-item">
              <a class="nav-link" href="authenticate.php">Sign in / Sign up</a>
            </li>
			<?php } ?>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Page Content -->
    <div class="container">