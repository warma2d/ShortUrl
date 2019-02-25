<?php 


require_once 'Code.php';
require_once 'database.php';

///////////////////////////////

    
$url = 'http://domain.com/your/long/url/?a=1&b=2&c=3';

$code = new Code($db, $url);

$shortCode = $code->get();

var_dump($shortCode);




















?>