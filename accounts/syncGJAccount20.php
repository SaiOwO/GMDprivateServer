<?php
//error_reporting(0);
include "../incl/lib/connection.php";
require "../incl/lib/generatePass.php";
require_once "../incl/lib/exploitPatch.php";
include_once "../config/security.php";
include_once "../incl/lib/defuse-crypto.phar";
use Defuse\Crypto\KeyProtectedByPassword;
use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;
$ep = new exploitPatch();
//here im getting all the data
$userName = $ep->remove($_POST["userName"]);
$pass2 = $_POST["password"];
$password = md5($pass2 . "epithewoihewh577667675765768rhtre67hre687cvolton5gw6547h6we7h6wh");
$secret = "";
$generatePass = new generatePass();
$pass = $generatePass->isValidUsrname($userName, $password);
if ($pass == 1) {
	$query = $db->prepare("select * from accounts where userName = :userName");
	$query->execute([':userName' => $userName]);
	$result = $query->fetchAll();
	$account = $result[0];
	$accountID = $account["accountID"];
	if(!is_numeric($accountID)){
		exit("-1");
	}
	if(!file_exists("../data/accounts/$accountID")){
			$saveData = $account["saveData"];
		if(substr($saveData,0,4) == "SDRz"){
			$saveData = base64_decode($saveData);
		}
	}else{
		if($cloudSaveEncryption == 1){
			$saveData = file_get_contents("../data/accounts/$accountID");
			if(file_exists("../data/accounts/keys/$accountID")){
				$protected_key_encoded = file_get_contents("../data/accounts/keys/$accountID");
				$protected_key = KeyProtectedByPassword::loadFromAsciiSafeString($protected_key_encoded);
				$user_key = $protected_key->unlockKey($pass2);
				try {
					$saveData = Crypto::decrypt($saveData, $user_key);
				} catch (Defuse\Crypto\Exception\WrongKeyOrModifiedCiphertextException $ex) {
					exit("-2");	
				}
			}
		}
	}
	echo $saveData.";21;30;a;a";
}else{
	echo -1;
}
?>