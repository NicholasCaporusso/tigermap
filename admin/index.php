<?php
session_start();

require_once('_helpers.php');
require_once('settings.php');

if(!Session::isUser(ROLES::USER,ROLES::USER)) header('location: authenticate.php');

require_once(__DIR__ .'/theme/header.php');
?>
<h1 class="mt30">Dashboard</h1>
<?php
require_once(__DIR__ .'/theme/footer.php');