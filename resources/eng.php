<?php
# Configurable Messages: English

# Guess Game Abort Messages
$gameaborted = 'Quiz has been aborted!!'; // If each player displayed
$nogameactive = 'There are no active games availible!'; // Displayed command user

# Guess Game Solution Messages
$normalsolution = 'Answer for Normal Game Solution:'; // Solution is displayed for Normal Number, after this message.
$squaresolution = 'Answer for Square Game Solution:'; // Solution is displayed for Square Number, after this message.

# Other Messages
$nopermission = 'You do not have permission to run this command!';
$gamealreadyactive = 'A quiz has already been activated!';

# Normal Guessing Game
$header = '------- Number Quiz -------';
$firstline = 'Writing in the chat a';
$secondline = "number zwischen§d {min} §bund§d {max}"; // {min} and {max} are the limits of the game
$thirdline = 'if its the number who sought';
$fourthline = 'is, you win something!';
$bottom = '------- Number Quiz -------';

# Error messages -- what is zwischen xd
$numtoohigh = "This number is too high, the quiz uses the numbers zwischen§d {min} §cund§d {max}!";
$notright = 'Unfortunately, that is not the correct answer!';

# Winner News
$congratulation = "Congratulations, {name}". '!';
$rightnumber = "The required number was: Once upon {number}";. '.'
$message = winner "You have {count} times {won itemname}!";
# {Count}: Number of price (Item)
# {Itemname}: Name of the price

# Squares Guessing Game
$qheader = '--- Perfect Square Quiz ---';
$qfirstline = 'Writing in the chat, the';
$qsecondline = "square number von§d {qnum}". '.';
$qthirdline = 'if its the number who sought';
$qfourthline = 'is, you win something!';
$qbottom = '--- Perfect Sqaure Quiz ---';

# Error Message
$qnotright = 'Unfortunately, that is not the correct square number!';

# Winner Message
$qcongratulation = 'Congratulations, {name}!';
$qrightnumber = "The perfect square von§9 {qnum} §6is§b {numq}"; // {qnum} is the output number and {numq} is the square number
$qwinnermessage = "You won {count} times {itemname}!";
# {Count}: Number of price (Item)
# {Itemname}: Name of the price

# Help message for both types of rate Game
$advice = 'To participate, your chat message must consist only of figures!';
?>
