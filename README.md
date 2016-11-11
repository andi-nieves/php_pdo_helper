# php_pdo_helper

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



