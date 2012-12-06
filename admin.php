<?php
session_start();

class Admin{

private $jsFile;

function __construct(){
	$this->jsFile = dirname(__FILE__) .'/smp.js';
	wp_enqueue_style('shutto', plugins_url('shutto.css', __FILE__), array(), '1.0.0');
	add_action('admin_menu', array($this, 'settingSubMenu') );
	add_filter( 'plugin_action_links', array($this, 'shutto_plugin_action_links'), 10, 2 );
}


function settingSubMenu(){
	add_submenu_page('plugins.php', __('shutto 設定 '), __('shutto 設定'), 8, 'shutto_config', array($this, 'shutto_config') );
}

function shutto_config(){
	$submit = false;
	$arrErrMsg = array();
	if( $_POST["submit_btn"] == "submit" ){ 
		$submit = true;
		$this->checkAccount(&$arrErrMsg);
		$this->checkPostBack(&$arrErrMsg);
		if(count($arrErrMsg) == 0){
			$this->submitProcess();
		}
	}
	$rString = $this->setPostback();
	$this->shutto_config_disp($arrErrMsg, $rString, $submit);
}

function shutto_config_disp($arrErrMsg, $rString, $submit){
	// plugins.php?page=shutto_configのHTMLの表示
	$flgSetAccount = $this->getSettingAccount();
	if( $flgSetAccount == false ){
?>
<div id='shutto-warning' class='updated fade'><p><strong>shutto はもうすぐ利用できます。</strong> 
利用するには、<a href='https://shutto.com/register' target='_blank'>shuttoのユーザを登録する</a>必要があります。</div>
<?php
}
?>


<a href="http://shutto.com/" target="_blank">
<img src="/wp-content/plugins/shutto/logo.png" />
</a><br/>
<h2><a href="http://shutto.com/" target="_blank">shutto</a>は、PCサイトをスマートフォンサイトに変換するサービスです。</h2>


<div class="narrow">



<form action="/wp-admin/plugins.php?page=shutto_config" method="post" name="frm1" style="margin: auto; width: 400px; ">
<p id="desc2">
<pre>
<b>shuttoについて</b>
<a href="http://shutto.com/" target="_blank">http://shutto.com/</a>

<b>shutto「使い方」について</b>
<a href="http://shutto.com/howto" target="_blank">http://shutto.com/howto</a>

<b>shuttoで変換をしてみる</b>
<?php 
	$format = '<a href="http://shutto.com/edit/%s/"  target="_blank">http://shutto.com/edit/%s/</a>';
	$str = sprintf($format, $_SERVER["HTTP_HOST"], $_SERVER["HTTP_HOST"]);
	echo $str;
?>


<b>shuttoプラグインについて</b>
このプラグインは、shuttoを使って変換したWordPressのサイトを、スマートフォン対応サイトにすることができます。

shuttoを利用するには、<a href="https://shutto.com/register" target="_blank">こちら</a>よりshuttoのユーザを登録していただき、
shuttoのユーザIDを以下のフォームに入力してください。

<?php 
if($flgSetAccount == false){
	echo '<p class="desc1">shuttoのユーザIDを以下のフォームに入力してください。</p>';
}else{
	echo '<p class="desc1">shuttoのユーザID: '. $flgSetAccount .'が設定されています。</p>';
}
?>
 

<?php 
if($submit == true && count($arrErrMsg) > 0){
	$errMsg = '';
	foreach($arrErrMsg as $k => $v){
		$errMsg .= $v ."<br/>";
	}
	echo '<div class="errMsg">'. $errMsg .'</div>';
}
?>

<?php 
if($submit == true && count($arrErrMsg) == 0){
	echo '<div class="successMsg">shuttoのユーザIDを登録しました。</div>';
}
?>

<label for="shutto_account" accesskey="n">shuttoのユーザID</label><input type="text" name="shutto_account" id="shutto_account" size="50" />
<input type="hidden" name="postback_key" value="<?php echo $rString ?>" />
<input type="hidden" name="submit_btn" value="" />
<center><input type="button" name="upload_btn" value="設定" onClick="document.frm1.submit_btn.value = 'submit'; document.frm1.submit();"/></center>
</form>
</div>

<?php
}



function shutto_plugin_action_links( $links, $file ) {
        if ( $file == plugin_basename( dirname(__FILE__).'/shutto.php' ) ) {
                $links[] = '<a href="plugins.php?page=shutto_config">'.__('Settings').'</a>';
        }

        return $links;
}


function setPostback(){
	unset($_SESSION["postback_key"]);
	$rString = $this->getRandomString();
	$_SESSION['postback_key'] = $rString;
	return $rString;
}

function checkPostback(&$arrErrMsg){
	if($_POST['postback_key'] != $_SESSION['postback_key']){
		$arrErrMsg['postback'] = '不正なアクセスです';
	}
}


function checkAccount($arrErrMsg){
	if(empty($_POST['shutto_account']) == true){
		$arrErrMsg['shutto_account'] = 'shuttoのユーザIDが入力されていません。';
		return;
	}
	
	
	if( preg_match('/^[a-zA-Z][a-zA-Z0-9_\-]{2,24}$/', trim($_POST['shutto_account'])) == 0 ){
		$arrErrMsg['shutto_account'] = 'shuttoのユーザIDの文字列ではありません。';
		return;
	}
	
	$convertJS = sprintf("http://shutto.com/embed/%s/convert.js", trim($_POST['shutto_account']) );
	if(file_get_contents($convertJS) == false){
		$arrErrMsg['shutto_account'] = 'shuttoのユーザIDではありません。';
		return;
	}
	

}

function submitProcess(){
	global $wpdb;
	$table_name = $wpdb->prefix . "shutto_settings";

	$status = $this->getSettingAccount();
	if($status == false){
		$insertStates = "INSERT INTO ". $table_name ." (id, account_name) VALUES (1, %s)";
		$wpdb->query( $wpdb->prepare( $insertStates, $_POST['shutto_account']) );
	}else{
		$updateStates = "UPDATE ". $table_name ." SET account_name = %s WHERE id = 1";
		$wpdb->query( $wpdb->prepare( $updateStates, $_POST['shutto_account']) );
	}
}
		
	
	

private function getRandomString($length=10){
	$sCharList = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789_";
    mt_srand();
	$sRes = "";

    for($i = 0; $i < $length; $i++){
    	$sRes .= $sCharList[mt_rand(0, strlen($sCharList) - 1)];
	}
    return $sRes;
}


function getSettingAccount(){
	global $wpdb;
	$table_name = $wpdb->prefix . "shutto_settings";
	
	$selectAccount = "SELECT  account_name FROM " . $table_name .
		" WHERE id = 1";
	$results = $wpdb->get_row($selectAccount);
	if( $results->account_name == NULL ){
		return false;
	}else{
		return $results->account_name;
	}
}

}

$objAdmin = new Admin();
