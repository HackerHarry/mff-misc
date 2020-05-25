<?php
// Buy-a-butterfly file for My Free Farm Bash Bot (front end)
//
if ($_POST) {
 @ini_set('zlib.output_compression', 0);
 @ini_set('implicit_flush', 1);
 for ($i = 0; $i < ob_get_level(); $i++)
  ob_end_flush();
 ob_implicit_flush(1);
 strpos($_POST["username"], ' ') === false ? $username = $_POST["username"] : $username = rawurlencode($_POST["username"]);
 $server = $_POST["server"];
 $password = $_POST["password"];
 $slot1 = $_POST["slot1"];
 $slot2 = $_POST["slot2"];
 $slot3 = $_POST["slot3"];
 $slot4 = $_POST["slot4"];
 $slot5 = $_POST["slot5"];
 $slot6 = $_POST["slot6"];
 $butterfly = $_POST["butterfly"];

 $cmd = "bash /var/www/html/mffbashbot/script/mffGetButterfly.sh $username $password $server $butterfly $slot1 $slot2 $slot3 $slot4 $slot5 $slot6";
 $descriptorspec = array(
  0 => array("pipe", "r"), // stolen from Stack Overflow
  1 => array("pipe", "w"),
  2 => array("pipe", "w"));
 print "<pre>\n";
 $process = proc_open($cmd, $descriptorspec, $pipes, NULL, NULL);
 if (is_resource($process))
  while ($s = fgets($pipes[1]))
   print $s;
 print "</pre>\n";
 exit(0);
}
include 'functions.php';
$butterflies = (json_decode('{"1":"Zitronenfalter","2":"Kleiner Fuchs","3":"Resedafalter","4":"Admiral","5":"C-Falter","6":"Baumweißling","7":"Schachbrett","8":"Argus Bläuling","9":"Aurorafalter","10":"Tagpfauenauge","11":"Schwalbenschwanz","12":"Krähe","13":"Monarch","14":"Zebrafalter","15":"Blauer Morpho","16":"Glasflügler","17":"Götterbaum-Spinner","18":"Atlasspinner","19":"Kometen-Motte","20":"Kleiner Feuerfalter","21":"Sumpfwiesen-Perlmuttfalter","22":"Weißdolch-Bläuling","23":"Roter Apollo","24":"Goldener Scheckenfalter","25":"Großer Feuerfalter","26":"Großer Schillerfalter","27":"Schwarzer Bär","28":"Segelfalter","29":"Rotrandbär","30":"Totenkopfschwärmer","31":"Mittlerer Weinschwärmer","32":"Brauner Bär","33":"Oleanderschwärmer","34":"Hornissen-Glasflügler","35":"Taubenschwänzchen"}', true));
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
 <head>
  <title>My Free Farm Bash Bot - Buy a butterfly</title>
  <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans&display=swap" rel="stylesheet">
  <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
  <link href="css/mffbot.css" rel="stylesheet" type="text/css">
 </head>
 <body id="main_body" class="main_body text-center">
  <script type="text/javascript">
   function writeError(whatsmissing) {
    document.getElementById("result").innerHTML = '<h4><font color="darkred">' + whatsmissing + ' fehlt!</font></h4>';
   }
   function buyButterfly() {
    var i, j;
    var sUser = document.forms.logon.username.value;
    var sPass = document.forms.logon.password.value;
    var iServer = document.forms.logon.server.value;
    var iButterfly = document.forms.logon.butterfly.value;
//    var iSlot = document.forms.logon.slot.value;
    if (sUser == "0") {
     writeError("Die Farmauswahl");
     return false;
    }
    if (!sPass) {
     writeError("Das Passwort");
     return false;
    }
    if (iServer == "0") {
     writeError("Die Servernummer");
     return false;
    }
    if (iButterfly == "0") {
     writeError("Die Schmetterlingswahl");
     return false;
    }
//    if (iSlot == "0") {
//     writeError("Die Slotauswahl");
//     return false;
//    }
    var sData = "username=" + sUser + "&server=" + iServer + "&password=" + sPass + "&butterfly=" + iButterfly;
     j = (document.querySelectorAll("[id*=slot]")).length;
     for (i = 0; i < j; i++) {
      if (document.querySelectorAll("[id*=slot]")[i].checked)
       sData += "&slot" + (document.querySelectorAll("[id*=slot]"))[i].value + "=" + (document.querySelectorAll("[id*=slot]"))[i].value;
     }
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
     if (xhttp.readyState != null && (xhttp.readyState < 3 || xhttp.status != 200))
      return;
     document.getElementById("result").innerHTML = xhttp.responseText;
     window.scrollTo(0,document.body.scrollHeight);
    }
    document.getElementById("result").innerHTML = "";
    xhttp.open("POST", "buy-a-butterfly.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(sData);
    return false;
   }

   function setFarmNo(farmname) {
   var farmno = [];
<?php
$username = "./";
include 'config.php';
system('cd ' . $gamepath . ' ; for farm in $(ls -d */ | tr -d \'/\'); do echo -n "   farmno[\"$farm\"] = "; grep server $farm/config.ini | awk \'{ printf "%i", $3 }\'; echo ";"; done');
unset($username);
?>
   document.forms.logon.serverdummy.options[farmno[farmname]].selected = true;
   document.forms.logon.server.value = farmno[farmname];
   return true;
   }
  </script>
  <h1>My Free Farm Bash Bot - Buy-a-butterfly</h1>
  <div class="form-group">
   <div class="offset-sm-5 col-sm-2">
    <a class="btn btn-outline-dark btn-sm" href="http://myfreefarm-berater.forumprofi.de/forumdisplay.php?fid=15" role="button">Forum</a>
   </div>
  </div>
  <form name="logon" method="post" action="">
   <div class="form-group">
    <div class="offset-sm-5 col-sm-2">
     <select name="serverdummy" class="form-control" disabled><option value="0">Server</option><option value="1">1</option><option value="2">2</option>
     <option value="3">3</option><option value="4">4</option><option value="5">5</option>
     <option value="6">6</option><option value="7">7</option><option value="8">8</option>
     <option value="9">9</option><option value="10">10</option><option value="11">11</option>
     <option value="12">12</option><option value="13">13</option><option value="14">14</option>
     <option value="15">15</option><option value="16">16</option><option value="17">17</option>
     <option value="18">18</option><option value="19">19</option><option value="20">20</option>
     <option value="21">21</option><option value="22">22</option><option value="23">23</option>
     <option value="24">24</option><option value="25">25</option></select>
    </div>
   </div>
   <input type="hidden" name="server" value="0">
   <div class="form-group">
    <div class="offset-sm-5 col-sm-2">
     <select name="username" class="form-control" onchange="return setFarmNo(document.forms.logon.username.options[document.forms.logon.username.options.selectedIndex].value);">
     <option value="0" selected>Farm</option>
<?php
$username = "./";
include 'config.php';
system("cd " . $gamepath . " ; ls -d */ | tr -d '/' | sed -e 's/^\\(.*\\)$/     <option>\\1<\\/option>/'");
unset($username);
?>
     </select>
    </div>
   </div>
   <div class="form-group">
    <div class="offset-sm-5 col-sm-2">
     <select name="butterfly" class="form-control">
     <option value="0" selected>Schmetterling</option>
<?php
 foreach ($butterflies as $key => $value)
	print "<option id=\"o" . $key . "\" value=\"" . $key . "\">" . $value  . "</option>\n";
?>
     </select>
    </div>
   </div>
   <div class="form-group">
    <div class="offset-sm-5 col-sm-2">
<!--     <select name="slot" class="form-control">
     <option value="0" selected>Slot</option>
     <option id="slot1" value="1">1</option>
     <option id="slot2" value="2">2</option>
     <option id="slot3" value="3">3</option>
     <option id="slot4" value="4">4</option>
     <option id="slot5" value="5">5</option>
     <option id="slot6" value="6">6</option>
     </select> -->
     <input type="checkbox" id="slot1" name="slot1" value="1">&nbsp;Slot 1&nbsp;&nbsp;<input type="checkbox" id="slot2" name="slot2" value="2">&nbsp;Slot 2&nbsp;&nbsp;<input type="checkbox" id="slot3" name="slot3" value="3">&nbsp;Slot 3<br>
     <input type="checkbox" id="slot4" name="slot4" value="4">&nbsp;Slot 4&nbsp;&nbsp;<input type="checkbox" id="slot5" name="slot5" value="5">&nbsp;Slot 5&nbsp;&nbsp;<input type="checkbox" id="slot6" name="slot6" value="6">&nbsp;Slot 6
    </div>
   </div>
   <div class="form-group">
    <div class="offset-sm-5 col-sm-2">
     <input class="form-control" type="password" name="password" placeholder="Password">
    </div>
   </div>
   <button type="submit" class="btn btn-lg btn-success" value="submit" onclick="return buyButterfly();">Kaufen !</button>
  </form>
  <br>
  <div id="result"><br></div>
 </body>
</html>
