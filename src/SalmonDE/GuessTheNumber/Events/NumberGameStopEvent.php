<?php
namespace SalmonDE\GuessTheNumber\Events;

use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class NumberGameStopEvent extends GuessTheNumberEvent {

    static public $handlerList = null;

    public function __construct(Main $plugin, NumberGame $game){
        parent::__construct($plugin, $game);
    }

}
