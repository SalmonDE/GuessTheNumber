<?php
namespace SalmonDE\GuessTheNumber\Events;

use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class NumberGameStartEvent extends GuessTheNumberEvent implements Cancellable {
	use CancellableTrait;

	public function __construct(Main $plugin, NumberGame $game){
		parent::__construct($plugin, $game);
	}
}
