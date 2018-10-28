<?php
namespace SalmonDE\GuessTheNumber\Events;

use pocketmine\Player;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class PlayerFailEvent extends GuessTheNumberEvent {

    private $player;
    private $answer;

    public function __construct(Main $plugin, NumberGame $game, Player $player, string $answer){
        parent::__construct($plugin, $game);
        $this->player = $player;
        $this->answer = $answer;
    }

    public function getPlayer(): Player{
        return $this->player;
    }

    public function getAnswer(): string{
        return $this->answer;
    }

}
