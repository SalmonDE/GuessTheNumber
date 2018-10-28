<?php
namespace SalmonDE\GuessTheNumber\Events;

use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class NumberGameStopEvent extends GuessTheNumberEvent {

    public function __construct(Main $plugin, NumberGame $game){
        parent::__construct($plugin, $game);
    }
}
