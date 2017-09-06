/**
Author: Andy F. Nieves
For inquiries contact me:
Email: andinieves151720@gmail.com
**/
 
class db {
    private $dbname;
    private $dbusername;
    private $dbhost;
    private $dbpassword;
    public  $prefix;
    public  $conn;
    public $tblmeta = "meta_table";
    function __construct(){
        $this->connect();
    }
    public function connect(){
        $this->dbname       = $dbname;
        $this->dbhost       = $host;
        $this->dbusername   = $user;
        $this->dbpassword   = $password;
        try{
            $this->conn = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8",$this->dbusername,$this->dbpassword);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
    public function query($sql,$params=array()){
        $stmt   = $this->conn->prepare($sql);
        if(count($params)>0):
            foreach($params as $key => $value):
                $stmt->bindValue($key,$value);
            endforeach;
        endif;
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    public function cmd($sql,$params=array()){
        $db     = $this->conn;
        if(!$db):
            return false;
        endif;
        $stmt   = $db->prepare($sql);
        if(count($params)>0):
            foreach($params as $key => $value):
                if(is_object($value) || is_array($value) ):
                    $value = json_encode($value);
                endif;
                $stmt->bindValue($key,$value);
            endforeach;
        endif;
        $stmt->execute();
        return $db->lastInsertId();
    }
    function _cmd($sql,$params=array()){
        $db         = $this->conn;
        $stmt   = $this->conn->prepare($sql);
        if(count($params)>0):
            foreach($params as $key => $value):
                $stmt->bindValue($key,$value);
            endforeach;
        endif;
        $stmt->execute();
    }
    public function isconnected(){
        if($this->conn):
            return $this->conn;
        endif;
    }
    public function close_db(){
        $this->conn = null;
    }
    public function update_setting($meta_key,$meta_value){
        $values = array(":meta_key"=>$meta_key,":meta_value"=>$meta_value);
        if(empty($this->get_setting($meta_key))):
            $this->cmd("INSERT INTO $this->tblmeta (meta_key,meta_value)VALUES(:meta_key,:meta_value)",$values);
        else:
            $this->cmd("UPDATE $this->tblmeta SET meta_value=:meta_value WHERE meta_key=:meta_key",$values);
        endif;
    }
    public function get_setting($meta_key){
        $value = array(":meta_key"=>$meta_key);
        $result = $this->query("SELECT meta_value FROM $this->tblmeta WHERE meta_key=:meta_key",$value);
        return count($result)==0 ? "" : $result[0]['meta_value'];
    }
    function generate_insert_sql($table, $array) {
    $fields=array_keys($array);
    $values=array_values($array);
    $fieldlist=implode(',', $fields);
    $qs=str_repeat("?,",count($fields)-1);
    $sql="INSERT INTO ".$table." (".$fieldlist.") VALUES (${qs}?)";
        $q = $this->conn->prepare($sql);
    return $q->execute($values);
  }
 
  function generate_update_sql($table, $id, $array) {
    $fields=array_keys($array);
    $values=array_values($array);
    $fieldlist=implode(',', $fields);
    $qs=str_repeat("?,",count($fields)-1);
    $firstfield = true;
 
    $sql = "UPDATE ".$table." SET";
    for ($i = 0; $i < count($fields); $i++) {
        if(!$firstfield) {
        $sql .= ", ";
        }
        $sql .= " ".$fields[$i]."=?";
        $firstfield = false;
    }
    $sql .= " WHERE id=?";
        array_push($values,$id);
        $q = $this->conn->prepare($sql);
        $q->execute($values);
    return $sql;
  }
 
function _double_encryption($string){
    $output = $this->encrypt($string);
    return $this->encrypt($output);
}
function _double_decryption($token){
    $output = $this->decrypt($token);
    return $this->decrypt($output);
}
function encrypt($string) {
    $output         = false;
    $encrypt_method     = "AES-256-CBC";
    $secret_key     = AUTH_KEY;
    $secret_iv      = AUTH_IV;
    $key        = hash('sha256', $secret_key);
    $iv         = substr(hash('sha256', $secret_iv), 0, 16);
    $output         = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
    $output         = base64_encode($output);
    return $output;
}
function decrypt($string) {
    $output         = false;
    $encrypt_method     = "AES-256-CBC";
    $secret_key     = AUTH_KEY;
    $secret_iv      = AUTH_IV;
    $key        = hash('sha256', $secret_key);
    $iv         = substr(hash('sha256', $secret_iv), 0, 16);
    $output         = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
 
    return $output;
}
 
    function time_stamp(){
        $date = Date('Y-m-d h:i:s');
        return $date;
    }
}
 
$db = new db();
?>
