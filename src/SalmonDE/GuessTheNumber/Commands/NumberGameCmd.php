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
use SalmonDE\GuessTheNumber\NumberGame;

class NumberGameCmd extends PluginCommand implements CommandExecutor {

    public function __construct(Main $owner){
        parent::__construct('numbergame', $owner);
        $this->setExecutor($this);
        $this->setPermission('guessthenumber.cmd');
        $this->setUsage('/numbergame <rand|exp|add|sub|multi|div|fac|solution|abort>');
        $this->setDescription('Main command of GuessTheNumber');
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        switch(strtolower($args[0] ?? '')){
            case 'rand':
                if($sender->hasPermission('guessthenumber.cmd.randomint')){
                    $this->startGame(NumberGame::RANDOM_INT_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'exp':
                if($sender->hasPermission('guessthenumber.cmd.exponent')){
                    $this->startGame(NumberGame::EXPONENT_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'add':
                if($sender->hasPermission('guessthenumber.cmd.addition')){
                    $this->startGame(NumberGame::ADDITION_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'sub':
                if($sender->hasPermission('guessthenumber.cmd.subtraction')){
                    $this->startGame(NumberGame::SUBTRACTION_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'multi':
                if($sender->hasPermission('guessthenumber.cmd.multiplication')){
                    $this->startGame(NumberGame::MULTIPLICATION_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'div':
                if($sender->hasPermission('guessthenumber.cmd.division')){
                    $this->startGame(NumberGame::DIVISION_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'fac':
                if($sender->hasPermission('guessthenumber.cmd.factorial')){
                    $this->startGame(NumberGame::FACTORIAL_GAME, $sender);
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            case 'solution':
                if($sender->hasPermission('guessthenumber.cmd.solution')){
                    if($this->getPlugin()->isGameRunning()){
                        $solution = $this->getPlugin()->getCurrentGame()->getSolution();
                        $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('cmd.solution', $solution));
                    }else{
                        $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.noGameRunning'));
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
                        $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.noGameRunning'));
                    }
                }else{
                    $sender->sendMessage(new TranslationContainer(TF::RED."%commands.generic.permission"));
                }
                break;

            default:
                return false;
        }

        return true;
    }

    protected function startGame(int $gameType, CommandSender $sender){
        if(!$this->getPlugin()->isGameRunning()){
            $game = $this->getPlugin()->createGame($gameType);

            $this->getPlugin()->startGame($game);
        }else{
            $sender->sendMessage(TF::RED.$this->getPlugin()->getMessage('general.gameAlreadyRunning'));
        }
    }

}
