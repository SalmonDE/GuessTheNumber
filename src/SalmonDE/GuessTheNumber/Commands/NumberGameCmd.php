<?php
declare(strict_types = 1);

namespace SalmonDE\GuessTheNumber\Commands;

use pocketmine\command\Command;
use pocketmine\command\CommandExecutor;
use pocketmine\command\CommandSender;
use pocketmine\command\PluginCommand;
use pocketmine\event\TranslationContainer;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Main;

class NumberGameCmd extends PluginCommand implements CommandExecutor {

    public function __construct(Main $owner){
        parent::__construct('numbergame', $owner);
        $this->setExecutor($this);
        $this->setPermission('guessthenumber.cmd');

        $gameNames = '';

        foreach($owner->getGames() as $game){
            $gameNames .= str_replace(' ', '', strtolower($game->getName())).'|';
        }

        $this->setUsage('/numbergame <'.$gameNames.'solution|abort>');
        $this->setDescription('Main command of GuessTheNumber');
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch($args[0] = str_replace(' ', '', strtolower($args[0] ?? ''))){
            case 'solution':
                if($sender->hasPermission('guessthenumber.cmd.solution')){
                    if($this->getPlugin()->isGameRunning()){
                        $solution = $this->getPlugin()->getCurrentGame()->getSolution();
                        $sender->sendMessage(TF::AQUA.$this->getPlugin()->getMessage('general.game.solutionOnly', $solution));
                    }else{
                        $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.game.notRunning'));
                    }
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'abort':
                if($sender->hasPermission('guessthenumber.cmd.abort')){
                    if($this->getPlugin()->isGameRunning()){
                        $this->getPlugin()->stopGame();
                    }else{
                        $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.game.notRunning'));
                    }
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            default:

                if($this->getPlugin()->gameExists($args[0])){
                    $game = $this->getPlugin()->getGameByName($args[0]);

                    if($sender->hasPermission($game->getStartPermission())){
                        $this->startGame($args[0], $sender);
                    }else{
                        $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                    }
                }else{
                    return false;
                }
        }

        return true;
    }

    protected function startGame(string $gameName, CommandSender $sender): void{
        if(!$this->getPlugin()->isGameRunning()){
            $game = $this->getPlugin()->getGameByName($gameName);

            $this->getPlugin()->startGame($game);
        }else{
            $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.game.alreadyRunning', $this->getPlugin()->getCurrentGame()->getName()).TF::RESET);
        }
    }

    public function updateAvailableGames(): void{
        $gameNames = '';

        foreach($this->getPlugin()->getGames() as $game){
            $gameNames .= str_replace(' ', '', strtolower($game->getName())).'|';
        }

        $this->setUsage('/numbergame <'.$gameNames.'solution|abort>');
    }
}
