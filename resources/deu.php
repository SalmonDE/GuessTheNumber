<?php
# Konfigurierbare Nachrichten

# /guessgameabort Nachrichten
$gameaborted = 'Das Quiz wurde abgebrochen!';//Wird jedem Spieler angezeigt
$nogameactive = 'Momentan ist kein Quiz aktiv!';//Wird dem Befehlbenutzer angezeigt

# /guessgamesolution Nachrichten
$normalsolution = 'Gesuchte Zahl: ';//Nach dieser Nachricht wird die Lösung angezeigt
$squaresolution = 'Die gesuchte Quadratzahl ist: ';//Nach dieser Nachricht wird die Lösung angezeigt

# Sonstige Nachrichten
$nopermission = 'Das darfst du nicht!';
$gamealreadyactive = 'Ein Quiz ist schon aktiv!';

# Normales Ratespiel
$header = '*-------Zahlenquiz-------*';
$firstline = 'Schreibe in den Chat eine';
$secondline = "Zahl zwischen§d {min} §bund§d {max}";//{min} und {max} sind die Grenzen des Spiels
$thirdline = 'wenn es die Zahl ist, die gesucht';
$fourthline = 'wird, gewinnst du etwas! :D';
$bottom = '*-------Zahlenquiz-------*';

# Fehlernachrichten
$numtoohigh = "Diese Zahl ist zu hoch! Das Quiz benutzt die Zahlen zwischen§d {min} §cund§d {max}";
$notright = 'Leider ist dies nicht die gesuchte Zahl! ;(';

# Gewinnernachrichten
$congratulation = "Herzlichen Glückwunsch, {name}".'!';
$rightnumber = "Die gesuchte Zahl war:§b {number}".'.';
$winnermessage = "Du hast {count} mal {itemname} gewonnen!";
# {count} : Anzahl des Preises (Item)
# {itemname} : Name des Preises

# Quadratzahlen Ratespiel
$qheader = '*---Quadratzahlenquiz---*';
$qfirstline = 'Schreibe in den Chat die';
$qsecondline = "Quadratzahl von§d {qnum}".'.';
$qthirdline = 'wenn es die Zahl ist, die gesucht';
$qfourthline = 'wird, gewinnst du etwas! :D';
$qbottom = '*---Quadratzahlenquiz---*';

# Fehlernachrichten
$qnotright = 'Leider ist dies nicht die gesuchte Quadratzahl! ;(';

# Gewinnernachrichten
$qcongratulation = 'Herzlichen Glückwunsch, {name}!';
$qrightnumber = "Die Quadratzahl von§9 {qnum} §6ist§b {numq}";//{qnum} ist die Ausgangszahl und {numq} ist die Quadratzahl
$qwinnermessage = "Du hast {count} mal {itemname} gewonnen!";
# {count} : Anzahl des Preises (Item)
# {itemname} : Name des Preises

# Hilfenachricht für beide Arten des Ratespieles
$advice = 'Um mitzumachen, muss deine Chatnachricht nur aus Zahlen bestehen!';
?>
