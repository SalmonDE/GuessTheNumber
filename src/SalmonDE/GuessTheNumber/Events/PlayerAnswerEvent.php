<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Events;

use pocketmine\player\Player;
use pocketmine\event\Cancellable;
use pocketmine\event\CancellableTrait;
use SalmonDE\GuessTheNumber\Games\NumberGame;
use SalmonDE\GuessTheNumber\Main;

class PlayerAnswerEvent extends GuessTheNumberEvent implements Cancellable {
	use CancellableTrait;

	private $player;
	private $answer;
	private $showInChat = false;

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

	public function setAnswer(string $answer): string{
		$this->answer = $answer;
	}

	public function showChatMessage(bool $value = true): void{
		$this->showInChat = $value;
	}
}
