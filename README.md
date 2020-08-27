# php_pdo_helper


#SETUP
This class requires you to put your db credentials in the connect function.

```php
public function connect(){
        $this->dbname       = "DB_NAME";
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

#SIMPLE WHERE QUERY
```php
//sample query usage (direct query) without param
$result = $db->query("SELECT * FROM `table` WHERE `id` = 1");
```
#QUERY WITH PARAMETER
```php
sample query usage with pdo parameter
$params = array(
	":field1" => 'value1',
	":field2" => 'value2'
)
$result = $db->query("SELECT * FROM `table` WHERE `field1` = :field1 AND `field2` = :field2",$params)
//query function will return query result
```

#CMD QUERY WITHOUT PARAMETER
```php
sample query usage without param
$id = $db->cmd("INSERT INTO `table` (`field1`,`field2`)values('value1','value2')");
```

#CMD WITH PARAMETER
```php
$params = array(
	":value1" => 'value1',
	":value2" => 'value2'
)
sample query usage with param
$id = $db->cmd("INSERT INTO `table` (`field1`,`field2`)values(':value1',':value2')",$params);
//cmd funcion will return last inserted id
```

** NOTE: Use parametized SQL to avoid SQL INJECTION.



