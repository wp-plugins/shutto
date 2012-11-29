<?php
require_once('admin.php');

class Front{

function __construct(){
	$objAdmin = new Admin();	
	$account = $objAdmin->getSettingAccount();
	if($account != false){
		$convertJs = sprintf("http://shutto.com/embed/%s/convert.js", $account);
		wp_enqueue_script('shutto', $convertJs, array(), '1.0.0');
	}
}

}

$objFront = new Front();
