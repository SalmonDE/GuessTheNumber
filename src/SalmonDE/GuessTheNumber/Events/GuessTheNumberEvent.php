<?php
namespace SalmonDE\GuessTheNumber\Events;

use pocketmine\event\plugin\PluginEvent;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class GuessTheNumberEvent extends PluginEvent {

    private $game;

    public function __construct(Main $plugin, NumberGame $game){
        parent::__construct($plugin);
        $this->game = $game;
    }

    public function getGame(): NumberGame{
        return $this->game;
    }
}
