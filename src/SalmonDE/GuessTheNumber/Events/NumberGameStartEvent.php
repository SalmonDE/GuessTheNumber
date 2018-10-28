<?php
namespace SalmonDE\GuessTheNumber\Events;

use pocketmine\event\Cancellable;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class NumberGameStartEvent extends GuessTheNumberEvent implements Cancellable {

    public function __construct(Main $plugin, NumberGame $game){
        parent::__construct($plugin, $game);
    }
}
