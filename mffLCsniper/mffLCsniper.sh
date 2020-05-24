#!/usr/bin/env bash
#
# Bauernwettbewerbs-Sniper für deutsche My Free Farm Server
# Offset zum Führenden kann positiv, negativ oder 0 sein
# 0 schmeisst dann genausoviel wie der Führende rein, 1 einen mehr, -1 einen weniger
# MAXAMOUNT ist halt eine Sicherheitsgrenze
# Nicht berücksichtigt werden Waren, die bereits von Spieler eingeworfen wurden!
#
# Harun "Harry" Basalamah, Oct 2019

: ${1:?Offset zum ersten Platz fehlt}
: ${2:?Maximaler Einwurf fehlt}

OFFSET=$1
MAXAMOUNT=$2
MFFUSER=deine-farm
MFFPASS=dein-passwort
MFFSERVER=deine-servernummer
JQBIN=$(which jq)
: ${JQBIN:?jq fehlt}
LOGFILE=/tmp/mffquest$$.log
OUTFILE=/tmp/questtemp$$.html
COOKIEFILE=/tmp/questcook$$.txt
rm -f $COOKIEFILE 2>/dev/null
NANOVALUE=$(echo $(($(date +%s%N)/1000000)))
LOGOFFURL="http://s${MFFSERVER}.myfreefarm.de/main.php?page=logout&logoutbutton=1"
POSTURL="https://www.myfreefarm.de/ajax/createtoken2.php?n=${NANOVALUE}"
AGENT="Mozilla/5.0 (Macintosh; Intel Mac OS X 10_8_2) AppleWebKit/537.17.13 (KHTML, like Gecko) Chrome/57.0.2940.56 Safari/537.17.13"
POSTDATA="server=${MFFSERVER}&username=${MFFUSER}&password=${MFFPASS}&ref=&retid="
AJAXURL="http://s${MFFSERVER}.myfreefarm.de/ajax/"

function login-MFF {
 echo "Login-Token fuer MFF server ${MFFSERVER} anfordern..."
 MFFTOKEN=$(wget -v -o $LOGFILE -O - --user-agent="$AGENT" --post-data="$POSTDATA" --keep-session-cookies --save-cookies $COOKIEFILE "$POSTURL" | sed -e 's/\[1,"\(.*\)"\]/\1/g' | sed -e 's/\\//g')
 echo "Anmeldung an MFF Server ${MFFSERVER} mit Benutzername $MFFUSER"
 wget -v -o $LOGFILE --output-document=$OUTFILE --user-agent="$AGENT" --keep-session-cookies --save-cookies $COOKIEFILE "$MFFTOKEN"
 RID=$(grep -om1 '[a-z0-9]\{32\}' $OUTFILE)
 if [ -z "$RID" ]; then
  echo "FATAL: keine RID erhalten"
  # abmelden...
  wget -v -o $LOGFILE --output-document=/dev/null --user-agent="$AGENT" --load-cookies $COOKIEFILE "$LOGOFFURL"
  exit 1
 fi
 echo "RID erhalten"
}

function logout-MFF {
 echo "Abmelden..."
 wget -v -o $LOGFILE --output-document=/dev/null --user-agent="$AGENT" --load-cookies $COOKIEFILE "$LOGOFFURL"
 rm $LOGFILE $OUTFILE $COOKIEFILE
}

login-MFF

AJAXMAIN="${AJAXURL}main.php?rid=${RID}&"

function SendAJAXMainRequest {
 local sAJAXSuffix=$1
 wget -v -o $LOGFILE --output-document=$OUTFILE --user-agent="$AGENT" --load-cookies $COOKIEFILE ${AJAXMAIN}${sAJAXSuffix}
}

echo "Bauernwettbewerbs-Infos einholen..."
SendAJAXMainRequest "action=initwbw"
ENDDATE=$($JQBIN -r '.datablock[1].wettbewerb_ende?' $OUTFILE)
: ${ENDDATE:?Kein Enddatum gefunden! Fortfahren nicht möglich.}
echo "Bauernwettbewerb endet $ENDDATE"
DAY=${ENDDATE:0:2}
MONTH=${ENDDATE:3:2}
# year and time, that is
YEAR=${ENDDATE##*.}
CURRENTEPOCH=$(date +%s)
ENDEPOCH=$(date -d "$MONTH/$DAY/$YEAR" +%s)
# ENDEPOCH=$((CURRENTEPOCH + 122))
SLEEPSECS=$((ENDEPOCH - CURRENTEPOCH - 60))
logout-MFF
echo "$SLEEPSECS Sekunden warten..."
sleep $SLEEPSECS

# rock 'n roll!
NANOVALUE=$(echo $(($(date +%s%N)/1000000)))
LOGOFFURL="http://s${MFFSERVER}.myfreefarm.de/main.php?page=logout&logoutbutton=1"
POSTURL="https://www.myfreefarm.de/ajax/createtoken2.php?n=${NANOVALUE}"
# we're roughly 60 secs away from the dead line
login-MFF
echo "Bauernwettbewerbs-Infos einholen..."
SendAJAXMainRequest "action=initwbw"
FIRST=$($JQBIN -r '.datablock[2][0] | select(.menge > 1).menge' $OUTFILE)
echo "Der erste Platz hat $FIRST Stück eingeworfen"
if [ $FIRST -gt $MAXAMOUNT ]; then
 echo "Das ist mehr, als Du bieten willst."
 logout-MFF
 exit 0
fi
AMOUNT=$((FIRST + OFFSET))
echo "Du willst $AMOUNT einwerfen"
echo -n "Anlegen"
# time to shoot...

CURRENTEPOCH=$(date +%s)
SNIPEREPOCH=$((ENDEPOCH - 20))
# we're gonna shoot 20 seconds before dead line
while [ $CURRENTEPOCH -lt $SNIPEREPOCH ]; do
 echo -n "."
 sleep 1
 CURRENTEPOCH=$(date +%s)
done
echo "und FEUER!!"
SendAJAXMainRequest "amount=${AMOUNT}&action=sendwbwproduct"
logout-MFF
echo "He's dead, Jim."
exit 0
