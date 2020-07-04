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
 if (!empty($_POST["slots"]))
  $configContents['slots'] = str_replace(",", " ", $_POST["slots"]);
 else
  exit(1);
 $slots = $configContents['slots'];
 if (!empty($_POST["butterfly"]))
  $configContents['butterfly'] = str_replace(",", " ", $_POST["butterfly"]);
 else
  exit(1);
 $butterfly = $configContents['butterfly'];

 $cmd = "bash /var/www/html/mffbashbot/script/mffGetButterfly.sh $username $password $server \"$butterfly\" \"$slots\"";

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
//    var iButterfly = document.forms.logon.butterfly.value;
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
//    if (iButterfly == "0") {
//     writeError("Die Schmetterlingswahl");
//     return false;
//    }
//    if (iSlot == "0") {
//     writeError("Die Slotauswahl");
//     return false;
//    }
    var sData = "username=" + sUser + "&server=" + iServer + "&password=" + sPass;
    j = (document.querySelectorAll("[id*=butterfly]")).length;
    sData += "&butterfly=";
    for (i = 0; i < j; i++) {
     if (document.querySelectorAll("[id*=butterfly]")[i].checked)
      sData += (document.querySelectorAll("[id*=butterfly]"))[i].value + ",";
    }
    if (sData.substring(sData.length-1, sData.length) == ",")
     sData = sData.substring(0, sData.length - 1);
    j = (document.querySelectorAll("[id*=slot]")).length;
    sData += "&slots=";
    for (i = 0; i < j; i++) {
     if (document.querySelectorAll("[id*=slot]")[i].checked)
      sData += (document.querySelectorAll("[id*=slot]"))[i].value + ",";
    }
    if (sData.substring(sData.length-1, sData.length) == ",")
     sData = sData.substring(0, sData.length - 1);
    xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
     if (xhttp.readyState != null && (xhttp.readyState < 3 || xhttp.status != 200))
      return;
     document.getElementById("result").innerHTML = xhttp.responseText;
     window.scrollTo(0,document.body.scrollHeight);
    }
    document.getElementById("result").innerHTML = "";
    xhttp.open("POST", "mffGetButterfly.php", true);
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
    <a class="btn btn-outline-dark btn-sm" href="http://myfreefarm-berater.forumprofi.de/f15-Bash-Bot.html" role="button">Forum</a>
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
     <input class="form-control" type="password" name="password" placeholder="Password">
    </div>
   </div>
   <div class="row justify-content-center">
    <div class="col-auto">
     <table id="slottbl" class="table table-responsive" border="1">
      <tr>
       <td align="left"><input type="checkbox" id="slot1" name="slot1" value="1">&nbsp;Slot 1</td>
       <td align="left"><input type="checkbox" id="slot2" name="slot2" value="2">&nbsp;Slot 2</td>
       <td align="left"><input type="checkbox" id="slot3" name="slot3" value="3">&nbsp;Slot 3</td>
      </tr>
      <tr>
       <td align="left"><input type="checkbox" id="slot4" name="slot4" value="4">&nbsp;Slot 4</td>
       <td align="left"><input type="checkbox" id="slot5" name="slot5" value="5">&nbsp;Slot 5</td>
       <td align="left"><input type="checkbox" id="slot6" name="slot6" value="6">&nbsp;Slot 6</td>
      </tr>
     </table>
    </div>
   </div>
   <div class="row justify-content-center">
    <div class="col-auto">
     <table id="butterflytbl" class="table table-responsive" border="1">
<!--      <tr><th colspan="8">Einen dieser Schmetterlinge pro Slot kaufen</th></tr> -->
<?php
for ($i = 1; $i < 35; $i++) {
 print "<tr>";
 for ($j = 0; $j <= 5; $j++) {
  if (isset($butterflies["$i"])) {
   print "<td align=\"left\">";
   print "<input type=\"checkbox\" id=\"butterfly" . $i . "\" name=\"butterfly" . $i . "\" value=\"" . $i . "\">&nbsp;" . $butterflies["$i"] . "\n";
   print "</td>";
   $i++;
  }
 }
 $i--;
 print "</tr>";
}
?>
     </table>
    </div>
   </div>
   <button type="submit" class="btn btn-lg btn-success" value="submit" onclick="return buyButterfly();">Kaufen !</button>
  </form>
  <br>
  <div id="result"><br></div>
 </body>
</html>
