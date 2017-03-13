<?php
/*
CREATE TABLE IF NOT EXISTS `test1` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `add_time` int(10) NOT NULL DEFAULT '0',
  `value` varchar(20) NOT NULL DEFAULT '0',
  `category` varchar(20) NOT NULL DEFAULT '0',
  `ist` varchar(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1000000000 ;
*/
include_once('simple_html_dom.php');

function multiSITE($site, $ist) {

    $datasite = Array();
    $text = $site;
    $html = str_get_html($text);

    if ($ist == '/temp/submit.html') {
        $a_links = $html->find('input');
        $a_links2 = $html->find('h3');
        $datasite['value'] = $a_links[0]->value; //value эл. submit - value
        $datasite['category'] = $a_links[1]->value; //value эл. id - категория
        $datasite['ist'] = $ist; //value эл. id - категория
        $datasite['time1'] = time(); //value эл. id - категория
    }

    if ($ist == '/temp/html.html') {
        $a_links3 = $html->find('h3', 0); // - value
        $a_links4 = $html->find('h4', 0); // - категория
        $datasite['value'] = $a_links3->innertext; //value эл. submit - value
        $datasite['category'] = $a_links4->innertext; //value эл. id - категория
        $datasite['ist'] = $ist; //value эл. id - категория
        $datasite['time1'] = time(); //value эл. id - категория
    }

    if ($ist == '/temp/json.json') {
        $json_response = $site;
        $obj1 = json_decode($json_response, true);
        $obj1['issue']['popup']['menuitem'];
        $ii = -1;
        foreach ($obj1['issue']['popup']['menuitem'] as $key => $value) {
            $ii++;
            $obj1['issue']['popup']['menuitem'][$ii]['ist'] = $ist;
            $obj1['issue']['popup']['menuitem'][$ii]['time1'] = time();
        }
        $datasite = $obj1['issue']['popup']['menuitem'];
    }
    return $datasite;
}

function multiHTTP($urlArr) {
    $sockets = Array(); // массив сокетов
    $urlInfo = Array();
    $retDone = Array();
    $retData = Array();
    $errno = Array();
    $errstr = Array();
    for ($x = 0; $x < count($urlArr); $x++) {
        $urlInfo[$x] = parse_url($urlArr[$x]);
        $urlInfo[$x][port] = ($urlInfo[$x][port]) ? $urlInfo[$x][port] : 80;
        $urlInfo[$x][path] = ($urlInfo[$x][path]) ? $urlInfo[$x][path] : "/";
        $sockets[$x] = fsockopen($urlInfo[$x][host], $urlInfo[$x][port], $errno[$x], $errstr[$x], 30);
        $retData['time1'] = time();
        $retData['ist'][$x] = $urlInfo[$x][path];

        fputs($sockets[$x], "GET " . $urlInfo[$x][path] . "$query HTTP/1.0\r\nHost: " .
                $urlInfo[$x][host] . "\r\n\r\n");
    }

    $done = false;
    while (!$done) {
        for ($x = 0; $x < count($urlArr); $x++) {
            if (!feof($sockets[$x])) {
                if ($retData[$x] || 1 == 1) {
                    $temp = fgets($sockets[$x], 128);
//                  if (isset($retData['text'][$x][6]))
                    if (isset($retData['text'][$x][10]))
                        $retData[$x] .= $temp;
                    $retData['text'][$x][] = $temp;
                } else {
                    $retData[$x] = ''; //fgets($sockets[$x],128); 
                }
            } else {
                $retDone[$x] = 1;
            }
        }
        $done = (array_sum($retDone) == count($urlArr));
    }
    return $retData;
}

function SendBD($MultiSendData) {
    $host = "localhost";
    $user = "test1";
    $pass = "test1";
    $db_name = "test1";
    $link = mysql_connect($host, $user, $pass);
    mysql_select_db($db_name, $link);
    $sql = mysql_query("INSERT INTO `test1` (`value`) 
                        VALUES ('" . $MultiSendData . "')");
    if ($sql) {
        echo "<p>Данные успешно добавлены в таблицу.</p>";
    } else {
        echo "<p>Произошла ошибка.</p>";
    }
}

function multiSEND($MultiSendData) {
    foreach ($MultiSendData as $key => $value) {
        if ($value["ist"] == "/temp/submit.html") {
            $xmlData1 .= '<value>' . $value["value"] . '</value><category>' . $value["category"] . '</category>';
        }
        if ($value["ist"] == "/temp/html.html") {
            $xmlData2 .= '<value>' . $value["value"] . '</value><category>' . $value["category"] . '</category>';
        }
        if ($value["ist"] == "/temp/json.json") {
            $xmlData3 .= '<value>' . $value["value"] . '</value><category>' . $value["category"] . '</category>';
        }
    }
    $xml1 = <<<XML
<?xml version='1.0' encoding="UTF-8?> 
<document xmlns="http://www.test.ru/">
<title>Zadaha-/temp/submit.html</title>
<body>
{$xmlData1}
</body>
</document>
XML;
    $xml2 = <<<XML
<?xml version='1.0' encoding="UTF-8?> 
<document xmlns="http://www.test.ru/">
<title>Zadaha-/temp/html.html</title>
<body>
{$xmlData2}
</body>
</document>
XML;
    $xml3 = <<<XML
<?xml version='1.0' encoding="UTF-8?> 
<document xmlns="http://www.test.ru/">
<title>Zadaha-/temp/json.json</title>
<body>
{$xmlData3}
</body>
</document>
XML;

// создаем обработчики
    $ch1 = curl_init();
    $ch2 = curl_init();
    $ch3 = curl_init();

// устанавливаем опции
    curl_setopt($ch1, CURLOPT_URL, "http://static.mymir.org/temp/ok");
    curl_setopt($ch1, CURLOPT_HEADER, 0);
    curl_setopt($ch1, CURLOPT_POST, true);
    curl_setopt($ch1, CURLOPT_POSTFIELDS, $xml1);
    curl_setopt($ch1, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch1, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch1, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch2, CURLOPT_URL, "http://static.mymir.org/temp/ok");
    curl_setopt($ch2, CURLOPT_HEADER, 0);
    curl_setopt($ch2, CURLOPT_POST, true);
    curl_setopt($ch2, CURLOPT_POSTFIELDS, $xml1);
    curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch2, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch2, CURLINFO_HEADER_OUT, true);

    curl_setopt($ch3, CURLOPT_URL, "http://static.mymir.org/temp/ok");
    curl_setopt($ch3, CURLOPT_HEADER, 0);
    curl_setopt($ch3, CURLOPT_POST, true);
    curl_setopt($ch3, CURLOPT_POSTFIELDS, $xml1);
    curl_setopt($ch3, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch3, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch3, CURLINFO_HEADER_OUT, true);

//create the multiple cURL handle
    $mh = curl_multi_init();

// добавляем обработчики
    curl_multi_add_handle($mh, $ch1);
    curl_multi_add_handle($mh, $ch2);

    $running = null;
    $active = null;
// выполняем запросы

    do {
        $mrc = curl_multi_exec($mh, $running);
        $mhinfo = curl_multi_info_read($mh);
        if (is_array($mhinfo) && ($ch = $mhinfo['handle'])) {
// один из запросов выполнен, можно получить информацию о нем
            $info = curl_getinfo($ch);
            $response122 = curl_exec($ch);
            if ($q != 1) {
                curl_multi_add_handle($mh, $ch3);
                $q = 1;
            }
            curl_multi_remove_handle($mh, $ch);
            SendBD("HTTP_CODE_" . $info['http_code']);
        }
    } while ($running > 0);

    curl_multi_close($mh);
}

$urls = Array();
$response1 = Array();
$urls[] = "http://static.mymir.org/temp/submit.html";
$urls[] = "http://static.mymir.org/temp/html.html";
$urls[] = "http://static.mymir.org/temp/json.json";

$a = multiHTTP($urls);
$ii = -1;

foreach ($a['ist'] as $key => $value) {
    $ii++;
    $obj1[$a['ist'][$ii]] = multiSITE($a[$ii], $a['ist'][$ii]);
}


$host = "localhost";
$user = "test1";
$pass = "test1";
$db_name = "test1";
$time = time();
$MultiSendData = Array();

$link = mysql_connect($host, $user, $pass);
mysql_select_db($db_name, $link);
foreach ($obj1 as $key => $value) {

    if ($key != '/temp/json.json') {
        $sql1 = mysql_query("SELECT max(`add_time`) as add_time from `test1` where `ist` = '" . $value['ist'] . "'", $link);
        $timeOld = mysql_fetch_array($sql1);
        if ($value['time1'] - ('0' . $timeOld['add_time']) > 60 * 60 * 24 && 1 == 1) {
            $sql = mysql_query("INSERT INTO `test1` (`add_time`, `value`, `category`, `ist`) 
                        VALUES ('" . $value['time1'] . "','" . $value['value'] . "','" . $value['category'] . "','" . $value['ist'] . "')");
        }
        $MultiSendData[] = $value;
    } else {
        $sql1 = mysql_query("SELECT max(`add_time`) as add_time from `test1` where `ist` = '" . $value[0]['ist'] . "'", $link);
        $timeOld = mysql_fetch_array($sql1);
        foreach ($value as $key => $value2) {
            if ($value2['time1'] - ('0' . $timeOld['add_time']) > 60 * 60 * 24 && 1 == 1) {
                $sql = mysql_query("INSERT INTO `test1` (`add_time`, `value`, `category`, `ist`) 
                        VALUES ('" . $value2['time1'] . "','" . $value2['value'] . "','" . $value2['category'] . "','" . $value2['ist'] . "')");
            }
            $MultiSendData[] = $value2;
        }
    }
}

if (isset($MultiSendData)) {
    multiSEND($MultiSendData);
}
?>
