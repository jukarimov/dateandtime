<!DOCTYPE html>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<html>
<head>
<script>

var test_autocomp = [
<?php
$db = mysql_connect("localhost", "root", "root") or die ("Can't connect to mysql");
mysql_SELECT_db("mydb", $db) or die ("oops:" . mysql_error());

$data = mysql_query("SELECT country_name FROM country");

while(($row = mysql_fetch_row($data)) != NULL) {
	echo '"' . $row[0] . '",';
}

?>
];

function sendhome() {
	window.location = '../time';
}

function log(msg) {
	console.log('LOG: ' + msg);
}

function getbyTag(tag) {
	return document.getElementsByTagName(tag);
}

function getbyId(id) {
	return document.getElementById(id);
}

function litup() {
	rows = getbyTag('tr');
	for (i=0; i < rows.length; i++) {
		rows[i].style.backgroundColor = i % 2 ? '#ccc' : '#fff';
	}
}

function show_updated_time() {
	var d = document.createElement('div');
	var t = (new Date());
	d.innerHTML = 'Your computer time: ' + t;
	d.style.position = 'absolute';
	d.style.top = '110px';
	d.style.left = '110px';
	document.body.appendChild(d);
}

function show_time_now() {
	var d = document.createElement('div');
	d.innerHTML = 'Now: ' + Date();
	d.style.position = 'absolute';
	d.style.top = '130px';
	d.style.left = '205px';
	d.id = "timer";
	document.body.appendChild(d);
}

function show_time_now_update() {
	var t = getbyId('timer');
	t.innerHTML = 'Now: ' + Date();
}
setInterval('show_time_now_update()', 1000);

function show_comp(data) {
	autocomp = getbyId('autocomp');
	autocomp.innerHTML = '';
	for (i in data) {
		autocomp.innerHTML += '<font><a href="../time/' + data[i] + '">' + data[i] + '</a></font>' + '<br>';
	}
}

function icsearch(s, str) {
	for (i in str) {
		if (s.toLowerCase() == str[i].toLowerCase())
			return 1;
	}
	return 0;
}

var AUTOINDX = -1;
var sug_list = [];
var search_func_calls = 0;
function search(e) {
	++search_func_calls;
	var key = e.keyCode;
	var val = getbyId('search').value;
	val = val.replace(' ', '_');
	val = val.toLowerCase();

	log('key:' + key);
	log('search:' + val + ';');
	//log(key);
	if (key == 13) {
		window.location = '/time/' + val;
	}
	else if (key == 8) {
		sug_list = [];
		var autocomp = getbyId('autocomp');
		autocomp.innerHTML = '';
	}

	var val = getbyId('search').value;
	var t = val;
	val = t.replace(' ', '_');
	var s = val;
	val = s.toLowerCase();
	if (val.length) {
		if (icsearch(t, test_autocomp) == 0)
			sug_list = [];
		var autocomp = getbyId('autocomp');
		autocomp.innerHTML = '';
		for (i=0; i < test_autocomp.length; i++) {
			if (test_autocomp[i].toLowerCase().match('^' + val)) {
				
				if (sug_list.indexOf(test_autocomp[i]) == -1)
					sug_list.push(test_autocomp[i]);
			}
		}
	}
	show_comp(sug_list);

	var val = getbyId('search').value;
	val = val.replace(' ', '_');
	val = val.toLowerCase();
	if (sug_list.length) {
		if (val == '') {
			var autocomp = getbyId('autocomp');
			autocomp.innerHTML = '';
		}

		for (i in sug_list)
			log('complete:' + sug_list[i]);

		show_comp(sug_list);

		if (key == 40 || key == 38) {
			
			var a = getbyTag('a');

			var key_down = key == 40;
			var key_up = key == 38;

			if (key_down && search_func_calls % 2 == 0) {
				AUTOINDX++;
			}
			else if (key_up && search_func_calls % 2 == 0) {
				AUTOINDX--;
			}
			if (AUTOINDX >= a.length || AUTOINDX < 0) {
				AUTOINDX = AUTOINDX < 0 ? 0 : AUTOINDX-1;
			}

			a[AUTOINDX].style.color = 'red';
			var i = getbyId('search');
			i.value = sug_list[AUTOINDX];
		}
	}
}

function search_init() {
	var i = getbyId("search");
	if (i.value.indexOf("search") != -1) {
		i.value = "";
	}
	i.onkeydown = search;
	i.onkeyup = search;
}
function search_check() {
	var i = getbyId("search");
	if (i.value == "")
		i.value = "search...";
	autocomp = getbyId("autocomp");
	//autocomp.innerHTML = '';
}
</script>
<style>

body {
	background-color: #666;
}

table {
	border: 1px solid #000;
	margin-left: 100px;
	margin-top: 95px;
	color: red;
	width: 600px;
	overflow: hidden;
	text-align: center;
}

td {
	border: 1px solid #000;
	border-radius: 5px;
	padding: 4px;
}

#search {
	position: absolute;
	left: 720px;
	top: 30px;
	color: #888;
	border: 1px solid rgba(0,0,0,0.3);
	-webkit-background-clip: border;
	-webkit-background-clip: padding-box;
	-webkit-background-clip: content-box;
	border-radius: 18px;
	height: 20px;
	font-size: 12pt;
}

#search:focus {
	color: #000;
}

#autocomp {
	position: absolute;
	left: 720px;
	top: 55px;
	width: 200px;
	background-color: #ddd;
	border-radius: 5px;
}

#autocomp a {
	text-decoration: none;
	color: #000;
}

</style>
</head>
<body onload="litup();show_updated_time();show_time_now();">
<input type="text" id="search" value="search..." onfocus="search_init()" onblur="search_check()"/>
<div id="autocomp"></div>
<?php

$req = $_SERVER['REQUEST_URI'];
$len = strlen($req);
$req_zone = "";

if ($req[5] == '/' && $len > 6)
{
	/* Extract query */
	for ($i=6; $i < $len; $i++)
	{
		$req_zone .= $req[$i];
	}
}
echo "<a href='../time'>home</a>";
echo "<h1>Date and Time: $req_zone</h1>";
echo "<table>";
echo '<tr><td>Time on server</td><td>' . date("Y-m-d H:i:s") . '</td></tr>';

$zone = "UTC";
date_default_timezone_set($zone);
echo "<tr><td>$zone </td><td>" . date("Y-m-d H:i:s") . '</td></tr>';

if ($req_zone != "")
{
	$zone = $req_zone;
	// actually thats js's job
	$zone = str_replace(" ", "_", $zone);
	$zone = str_replace("%20", "_", $zone);

	/* By zone name */
	$data = mysql_query("SELECT zone_name FROM zone WHERE zone_name LIKE '".$zone."%';", $db);
	if (!$data)
		die ("oops:" . mysql_error());
	$c=0;
	while ($row = mysql_fetch_row($data)) {
		$c++;
		date_default_timezone_set($row[0]);
		echo "<tr><td>$row[0]</td><td>" . date("Y-m-d H:i:s") . '</td></tr>';
	}

	/* By country code */
	if (!$c) {
		$data = mysql_query("SELECT zone_name FROM zone WHERE country_code = '".$zone."';", $db);
		if (!$data)
			die ("oops:" . mysql_error());
		while ($row = mysql_fetch_row($data)) {
			$c++;
			date_default_timezone_set($row[0]);
			echo "<tr><td>$row[0]</td><td>" . date("Y-m-d H:i:s") . '</td></tr>';
		}
	}

	/* By country name */
	if (!$c) {
		$data = mysql_query("SELECT zone_name FROM zone where country_code = 
				     (SELECT country_code FROM country where country_name='".$zone."')");
		if (!$data)
			die ("oops:" . mysql_error());
		while ($row = mysql_fetch_row($data)) {
			$c++;
			date_default_timezone_set($row[0]);
			echo "<tr><td>$row[0]</td><td>" . date("Y-m-d H:i:s") . '</td></tr>';
		}
	}

	$str = str_replace("_", " ", $zone);
	/* try again with spaces */
	if (!$c) {
		$data = mysql_query("SELECT zone_name FROM zone where country_code = 
				     (SELECT country_code FROM country where country_name='".$str."')");
		if (!$data)
			die ("oops:" . mysql_error());
		while ($row = mysql_fetch_row($data)) {
			$c++;
			date_default_timezone_set($row[0]);
			echo "<tr><td>$row[0]</td><td>" . date("Y-m-d H:i:s") . '</td></tr>';
		}
	}

	/* By city name */
	if (!$c) {
		$data = mysql_query("SELECT zone_name FROM zone where zone_name LIKE '%".$zone."%'");
		if (!$data)
			die ("oops:" . mysql_error());
		while ($row = mysql_fetch_row($data)) {
			$c++;
			date_default_timezone_set($row[0]);
			echo "<tr><td>$row[0]</td><td>" . date("Y-m-d H:i:s") . '</td></tr>';
		}
	}

	if (!$c) {
		echo "<script>alert('".$zone." not found')</script>";
		echo "<script>sendhome();</script>";
	}
}

echo "</table>";

mysql_close($db) or die(mysql_error());
?>
</body>
</html>
