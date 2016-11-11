<?php
/*this is for the enrypt/decrypt settings*/
define("AUTH_KEY","PUT_YOUR_AUTH_KEY_HERE");
define("AUTH_IV","PUT_YOUR_AUTH_IV_HERE");

/*REPLACE THIS CONSTANT VARS WITH YOUR DATABASE LOGIN CREDENTIALS*/
define("DBNAME","PUT_YOUR_DATABASE_NAME_HERE");
define("DBHOST","localhost"); //usually the hostname is localhost
define("DBUSERNAME","PUT_YOUR_DATABASE_USER_NAME_HERE");
define("DBPASSWORD","PUT_YOUR_DATABASE_PASSWORD_HERE");

class db_helper {
	private $dbname;
	private $dbusername;
	private $dbhost;
	private $dbpassword;
	public 	$prefix;
	public 	$connection;
	function __construct(){

		$this->dbname 			= DBNAME;
		$this->dbhost				= DBHOST;
		$this->dbusername 	= DBUSERNAME;
		$this->dbpassword 	= DBPASSWORD;
		try{
			$this->connection = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8",$this->dbusername,$this->dbpassword);
			$this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}catch(PDOException $e){
			echo $e->getMessage();
		}
	}
	public function isconnected(){
		if($this->connection):
			return $this->connection;
		endif;
	}
	public function close_db(){
		$this->connection = null;
	}
	function query($sql,$params=array()){
		if(!$this->connection):
			return false;
		endif;
		$stmt 	= $this->connection->prepare($sql);
		if(count($params)>0):
			foreach($params as $key => $value):
				$value = is_array($value) ? serialize($value) : $value;
				$stmt->bindValue($key,$value);
			endforeach;
		endif;
		$stmt->execute();
		return $stmt->fetchAll();
	}
	function cmd($sql,$params=array()){
		$stmt 	= $this->connection->prepare($sql);
		if(count($params)>0):
			foreach($params as $key => $value):
				$value = is_array($value) ? serialize($value) : $value;
				echo "[$key => $value]";
				$stmt->bindValue($key,$value);
			endforeach;
		endif;
		$stmt->execute();
		return $this->connection->lastInsertId();
	}
	function cmd2($sql,$params=array()){
		$stmt 	= $this->connection->prepare($sql);
		if(count($params)>0):
			foreach($params as $key => $value):
				$value = is_array($value) ? serialize($value) : $value;
				echo "[$key => $value]";
				$stmt->bindValue($key,$value);
			endforeach;
		endif;
		$stmt->execute();
	}
	function validate_password($password){
		//if(!preg_match('/^(?=.*d)(?=.*[A-Za-z])[0-9A-Za-z!@#$%]{8,50}$/', $password)):
		if(!preg_match('/^([A-Za-z])[0-9A-Za-z!@#$%_{}]{8,50}$/', $password)):
			return false;
		else:
			return  true;
		endif;
	}
	function validate_email($email) {
	    // First, we check that there's one @ symbol, and that the lengths are right
	    if (!preg_match("/^[^@]{1,64}@[^@]{1,255}$/", $email)) {
	        // Email invalid because wrong number of characters in one section, or wrong number of @ symbols.
	        return false;
	    }
	    // Split it into sections to make life easier
	    $email_array = explode("@", $email);
	    $local_array = explode(".", $email_array[0]);
	    for ($i = 0; $i < sizeof($local_array); $i++) {
	         if (!preg_match("/^(([A-Za-z0-9!#$%&'*+\/=?^_`{|}~-][A-Za-z0-9!#$%&'*+\/=?^_`{|}~\.-]{0,63})|(\"[^(\\|\")]{0,62}\"))$/", $local_array[$i])) {
	            return false;
	        }
	    }
	    if (!preg_match("/^\[?[0-9\.]+\]?$/", $email_array[1])) { // Check if domain is IP. If not, it should be valid domain name
	        $domain_array = explode(".", $email_array[1]);
	        if (sizeof($domain_array) < 2) {
	                return false; // Not enough parts to domain
	        }
	        for ($i = 0; $i < sizeof($domain_array); $i++) {
	            if (!preg_match("/^(([A-Za-z0-9][A-Za-z0-9-]{0,61}[A-Za-z0-9])|([A-Za-z0-9]+))$/", $domain_array[$i])) {
	                return false;
	            }
	        }
	    }
	    return true;
	}
	function encrypt($string) {
	    $output 		= false;
	    $encrypt_method 	= "AES-256-CBC";
	    $key 		= hash('sha256', AUTH_KEY);
	    $iv 		= substr(hash('sha256', AUTH_IV), 0, 16);
	    $output 		= openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
	    $output 		= base64_encode($output);
	    return $output;
	}
	function decrypt($string) {
	    $output 		= false;
	    $encrypt_method 	= "AES-256-CBC";
	    $key 		= hash('sha256', AUTH_KEY);
	    $iv 		= substr(hash('sha256', AUTH_IV), 0, 16);
	    $output 		= openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
	    return $output;
	}
	/*
		SAMPLE usage
		echo $db->generate_slug("Sample Slug");

		output:
		sample-slug
	*/
	function generate_slug($input){
		$input = html_decode($input);
		$input = str_replace("/"," ",$input);
		$input = preg_replace('/\s+/', '-', $input);
		$input = preg_replace("/[^a-zA-Z0-9\-.]/", "", $input);
		$input = htmlentities($input);
		$input = str_replace(" ","-",$input);
		$input = strtolower($input);
		$input = str_replace("--",'-',$input);
		return $input;
	}
	function generateStrongPassword($length = 9, $add_dashes = false, $available_sets = 'luds'){
		$sets = array();
		if(strpos($available_sets, 'l') !== false)
			$sets[] = 'abcdefghjkmnpqrstuvwxyz';
		if(strpos($available_sets, 'u') !== false)
			$sets[] = 'ABCDEFGHJKMNPQRSTUVWXYZ';
		if(strpos($available_sets, 'd') !== false)
			$sets[] = '23456789';
		if(strpos($available_sets, 's') !== false)
			$sets[] = '!@#$%&*?';
		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];
		$password = str_shuffle($password);
		if(!$add_dashes)
			return $password;
		$dash_len = floor(sqrt($length));
		$dash_str = '';
		while(strlen($password) > $dash_len)
		{
			$dash_str .= substr($password, 0, $dash_len) . '-';
			$password = substr($password, $dash_len);
		}
		$dash_str .= $password;

		return $dash_str;
	}
	function time_stamp(){
		$date = Date('Y-m-d h:i:s');
		return $date;
	}
	function readable_timestamp($date_2 , $date_1 = "" ){
			$date_1 = empty($date_1) ? date_today() : $date_1;
	    $result = "";
	    $datetime1 = date_create($date_1);
	    $datetime2 = date_create($date_2);
	    $interval = date_diff($datetime1, $datetime2);
	    $min = $interval->format("%i");
	    if($min<=5):
	    	$result = "Just now";
	    else:
	    	$result = "$min mins ago";
	    endif;
	    $hour = $interval->format("%h");
	    if($hour>0):
		    if($hour==1):
		    	$result = "$hour hour ago";
		    else:
		    	$result = "$hour hours ago";
		    endif;
	    endif;
	    $day = $interval->format("%d");
	    if($day>0):
		    if($day==1):
		    	$result = "Yesterday";
		    else:
		    	$date = date_create($date_2);
		    	//$result = "$day days ago";
		    	$result =  date_format($date,"M d");
		    endif;
	    endif;
	    $year = $interval->format("%y");
	    if($year>0):
	    		$date = date_create($date_2);
		    	$result =  date_format($date,"M d, Y");
	    endif;
	    if($date_2=='0000-00-00 00:00:00'):
	      $result = "-";
	    endif;

	    return $result;
	}
	function array_to_object($array) {
	    return (object) $array;
	}
	function object_to_array($object) {
		return (array) $object;
	}
}

/*
	sample query usage (direct query) without param
	$result = query("SELECT * FROM `table` WHERE `id` = 1");

	sample query usage with pdo parameter
	$params = array(
		":field1" => 'value1',
		":field2" => 'value2'
	)
	$result = query("SELECT * FROM `table` WHERE `field1` = :field1 AND `field2` = :field2",$params)
*/

/*
	sample query usage without param
	$id = cmd("INSERT INTO `table` (`field1`,`field2`)values('value1','value2')");

	$params = array(
		":value1" => 'value1',
		":value2" => 'value2'
	)
	sample query usage with param
	$id = cmd("INSERT INTO `table` (`field1`,`field2`)values(':value1',':value2')",$params);
*/



?>
