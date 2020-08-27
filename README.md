# php_pdo_helper

This class will requires you to put your db credentials
```php
$dbname

$host

$user

$password
```

```php
public function connect(){
        $this->dbname       = "DN_NAME";
        $this->dbhost       = "DB_HOST";
        $this->dbusername   = "DB_USERNAME";
        $this->dbpassword   = "DB_PASSWORD";
        try{
            $this->conn = new PDO("mysql:host=$this->dbhost;dbname=$this->dbname;charset=utf8",$this->dbusername,$this->dbpassword);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }catch(PDOException $e){
            echo $e->getMessage();
        }
    }
```
```php
//sample query usage (direct query) without param
$result = $db->query("SELECT * FROM `table` WHERE `id` = 1");
```

```php
sample query usage with pdo parameter
$params = array(
	":field1" => 'value1',
	":field2" => 'value2'
)
$result = $db->query("SELECT * FROM `table` WHERE `field1` = :field1 AND `field2` = :field2",$params)
//query function will return query result
```
```php
sample query usage without param
$id = $db->cmd("INSERT INTO `table` (`field1`,`field2`)values('value1','value2')");
```
```php
$params = array(
	":value1" => 'value1',
	":value2" => 'value2'
)
sample query usage with param
$id = $db->cmd("INSERT INTO `table` (`field1`,`field2`)values(':value1',':value2')",$params);
//cmd funcion will return last inserted id
```



