<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\command\utils\InvalidCommandSyntaxException;
use pocketmine\event\TranslationContainer;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginOwned;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Main;

class NumberGameCmd extends Command implements PluginOwned {

	private $owner;

	public function __construct(Main $owner){
		parent::__construct('numbergame', 'Main command of GuessTheNumber');
		$this->setPermission('guessthenumber.cmd');

		$gameNames = '';

		foreach($owner->getGames() as $game){
			$gameNames .= str_replace(' ', '', strtolower($game->getName())).'|';
		}

		$this->setUsage('/numbergame <'.$gameNames.'solution|abort>');
		$this->owner = $owner;
	}

	public function execute(CommandSender $sender, string $label, array $args){
		if(!$this->owner->isEnabled()){
			return false;
		}

		if(!$this->testPermission($sender)){
            return false;
        }

		$success = true;

		switch($args[0] = str_replace(' ', '', strtolower($args[0] ?? ''))){
			case 'solution':
				if($sender->hasPermission('guessthenumber.cmd.solution')){
					if($this->getOwningPlugin()->isGameRunning()){
						$solution = $this->getOwningPlugin()->getCurrentGame()->getSolution();
						$sender->sendMessage(TF::AQUA.$this->getOwningPlugin()->getMessage('general.game.solutionOnly', $solution));
					}else{
						$sender->sendMessage(TF::RED.$this->getOwningPlugin()->getMessage('general.game.notRunning'));
					}
				}else{
					$sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
				}
				break;

			case 'abort':
				if($sender->hasPermission('guessthenumber.cmd.abort')){
					if($this->getOwningPlugin()->isGameRunning()){
						$this->getOwningPlugin()->stopGame();
					}else{
						$sender->sendMessage(TF::RED.$this->getOwningPlugin()->getMessage('general.game.notRunning'));
					}
				}else{
					$sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
				}
				break;

			default:

				if($this->getOwningPlugin()->gameExists($args[0])){
					$game = $this->getOwningPlugin()->getGameByName($args[0]);

					if($sender->hasPermission($game->getStartPermission())){
						$this->startGame($args[0], $sender);
					}else{
						$sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
					}
				}else{
					$success = false;
				}
		}

		if(!$success){
			throw new InvalidCommandSyntaxException();
		}
	}

	protected function startGame(string $gameName, CommandSender $sender): void{
		if(!$this->getOwningPlugin()->isGameRunning()){
			$game = $this->getOwningPlugin()->getGameByName($gameName);

			$this->getOwningPlugin()->startGame($game);
		}else{
			$sender->sendMessage(TF::RED.$this->getOwningPlugin()->getMessage('general.game.alreadyRunning', $this->getOwningPlugin()->getCurrentGame()->getName()).TF::RESET);
		}
	}

	public function updateAvailableGames(): void{
		$gameNames = '';

		foreach($this->getOwningPlugin()->getGames() as $game){
			$gameNames .= str_replace(' ', '', strtolower($game->getName())).'|';
		}

		$this->setUsage('/numbergame <'.$gameNames.'solution|abort>');
	}

	public function getOwningPlugin(): Plugin{
		return $this->owner;
	}
}
