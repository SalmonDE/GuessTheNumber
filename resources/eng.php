<?php
# Configurable Messages: English

# Guess Game Abort Messages
$gameaborted = 'Quiz has been aborted!!'; // If each player displayed
$nogameactive = 'There are no active games availible!'; // Displayed command user

# Guess Game Solution Messages
$normalsolution = 'Normal Game Solution:'; // Solution is displayed for Normal Number, after this message.
$squaresolution = 'Square Game Solution:'; // Solution is displayed for Square Number, after this message.

# Other Messages
$nopermission = 'You do not have permission to execute this command!';
$gamealreadyactive = 'A quiz has already been activated!';

# Normal Guessing Game
$header = '------- Number Quiz -------';
$firstline = 'First, write a number in chat';
$secondline = "between§d {min} §band§d {max}."; // {min} and {max} are the limits of the game
$thirdline = 'If the guesser is correct,';
$fourthline = 'the guesser will win something!';
$bottom = '------- Number Quiz -------';

# Error Messages
$numtoohigh = "This number is too high, the quiz uses the numbers zwischen§d {min} §cund§d {max}!";
$notright = 'Unfortunately, that is not the correct answer!';

# Winner Messages
$congratulation = "Congratulations, {name}". '!';
$rightnumber = "The required number was: {number}";
$message = "You have been rewarded {count} of {itemname}!";
# {count}: Amount of the item they are given.
# {itemname}: Name of the Prize! (ex: dirt)

# Squares Guessing Game
$qheader = '--- Square Root Quiz ---';
$qfirstline = 'First, write a number in chat';
$qsecondline = "the square root of §d{qnum}";
$qthirdline = 'If the guesser is correct,';
$qfourthline = 'the guesser will win something!';
$qbottom = '--- Square Root Quiz ---';

# Error Message for Square
$qnotright = 'Unfortunately, that is not the correct square number!';

# Winner Message
$qcongratulation = 'Congratulations, {name}!';
$qrightnumber = "The sqaure root of §9{qnum} §6is §b{numq}"; 
#{qnum} is the output number
#{numq} is the square number

$qwinnermessage = "You have been rewarded {count} of {itemname}!";
# {Count}: Number of price (Item)
# {Itemname}: Name of the price

# Help Message
$advice = 'To participate, your chat message must only consist of figures!';
?>
