#!/usr/bin/env bash
#
# Verschickt eine Nachricht an mehrere My Free Farm Benutzer
#
# Dieses Skript ist NICHT zum MISSBRAUCH bestimmt!
#
# Harun "Harry" Basalamah, Feb 2021

MFFUSER=deine-farm
MFFPASS=dein-passwort
MFFSERVER=deine-servernummer
JQBIN=$(which jq)
: ${JQBIN:?jq fehlt}
COOKIEFILE=/tmp/msgcookie$$.txt
OUTFILE=/tmp/msgtemp$$.html
INFILE=users.txt
TMPFILE=tmpusers.txt
SUBJECT='"Betreff [MAX. 25 zeichen]"'
BODY='"Hier kommt der Textkörper hinein.\n
Er darf mehrzeilig sein und Sonderzeichen enthalten.\n
Es folgt eine Leerzeile...\n
\n
Grussformel\n
Farm XYZ"'
# anzahl benutzer, die angeschrieben werden...hier: 3
USERS=$(head -3 $INFILE >$TMPFILE)
mapfile -t aUSERS <$TMPFILE
NUMUSERS=${#aUSERS[*]}
INDEX=0
rm -f $COOKIEFILE 2>/dev/null
NANOVALUE=$(echo $(($(date +%s%N)/1000000)))
LOGOFFURL="http://s${MFFSERVER}.myfreefarm.de/main.php?page=logout&logoutbutton=1"
POSTURL="https://www.myfreefarm.de/ajax/createtoken2.php?n=${NANOVALUE}"
AGENT="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/44.0.2403.157 Safari/537.36"
POSTDATA="server=${MFFSERVER}&username=${MFFUSER}&password=${MFFPASS}&ref=&retid="
AJAXURL="http://s${MFFSERVER}.myfreefarm.de/ajax/"

function login-MFF {
 echo "Login-Token fuer MFF server ${MFFSERVER} anfordern..."
 MFFTOKEN=$(wget -q -O - --user-agent="$AGENT" --post-data="$POSTDATA" --keep-session-cookies --save-cookies $COOKIEFILE "$POSTURL" | sed -e 's/\[1,"\(.*\)"\]/\1/g' | sed -e 's/\\//g')
 echo "Anmeldung an MFF Server ${MFFSERVER} mit Benutzername $MFFUSER"
 wget -q --output-document=$OUTFILE --user-agent="$AGENT" --keep-session-cookies --save-cookies $COOKIEFILE "$MFFTOKEN"
 RID=$(grep -om1 '[a-z0-9]\{32\}' $OUTFILE)
 if [ -z "$RID" ]; then
  echo "FATAL: keine RID erhalten"
  # abmelden...
  wget -q --output-document=/dev/null --user-agent="$AGENT" --load-cookies $COOKIEFILE "$LOGOFFURL"
  exit 1
 fi
 echo "Anmeldung erfolgreich"
}

function logout-MFF {
 echo "Abmelden..."
 wget -q --output-document=/dev/null --user-agent="$AGENT" --load-cookies $COOKIEFILE "$LOGOFFURL"
 rm -f $TMPFILE $COOKIEFILE $OUTFILE
}

login-MFF

AJAXMSG="${AJAXURL}messages.php?mode=send&rid=${RID}&"

function SendAJAXMsgRequest {
 local sAJAXSuffix=$1
 wget -q --output-document=/dev/null --user-agent="$AGENT" --load-cookies $COOKIEFILE ${AJAXMSG}${sAJAXSuffix}
}

SUBJECT=$(echo $SUBJECT | jq -r '@uri')
BODY=$(echo $BODY | jq -r '@uri')
while [ $INDEX -lt $NUMUSERS ]; do
 echo "Nachricht $((INDEX + 1)) von $NUMUSERS senden..."
 aUSERS[$INDEX]=$(echo '"'${aUSERS[$INDEX]}'"' | jq -r '@uri')
 SendAJAXMsgRequest "name=${aUSERS[$INDEX]}&subject=${SUBJECT}&body=${BODY}"
 sleep 2
 INDEX=$((INDEX+1))
done

logout-MFF
