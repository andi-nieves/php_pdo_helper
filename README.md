# php_pdo_helper

This class will requires you to put your db credentials
$dbname
$host
$user
$password
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

sample query usage (direct query) without param
$result = query("SELECT * FROM `table` WHERE `id` = 1");

sample query usage with pdo parameter
$params = array(
	":field1" => 'value1',
	":field2" => 'value2'
)
$result = query("SELECT * FROM `table` WHERE `field1` = :field1 AND `field2` = :field2",$params)
//query function will return query result

sample query usage without param
$id = cmd("INSERT INTO `table` (`field1`,`field2`)values('value1','value2')");

$params = array(
	":value1" => 'value1',
	":value2" => 'value2'
)
sample query usage with param
$id = cmd("INSERT INTO `table` (`field1`,`field2`)values(':value1',':value2')",$params);
//cmd funcion will return last inserted id



