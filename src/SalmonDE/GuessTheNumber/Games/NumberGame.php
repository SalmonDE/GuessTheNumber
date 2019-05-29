<?php
namespace SalmonDE\GuessTheNumber\Games;

use pocketmine\item\Item;
use pocketmine\level\sound\AnvilFallSound;
use pocketmine\level\sound\ClickSound;
use pocketmine\level\sound\FizzSound;
use pocketmine\math\Vector3;
use pocketmine\Player;
use pocketmine\utils\TextFormat as TF;
use SalmonDE\GuessTheNumber\Events\PlayerFailEvent;
use SalmonDE\GuessTheNumber\Events\PlayerWinEvent;
use SalmonDE\GuessTheNumber\Main;

abstract class NumberGame {

	private $gameName;
	protected $playPermission;
	protected $startPermission;
	protected $solution = null;
	protected $calculation = null;
	protected $firstInt = null;
	protected $firstIntMin = null;
	protected $firstIntMax = null;
	protected $secondInt = null;
	protected $secondIntMin = null;
	protected $secondIntMax = null;
	protected $itemPrizes = [];

	public function __construct(string $name, array $prizes, string $playPermission, string $startPermission){
		$this->gameName = $name;
		$this->playPermission = $playPermission;
		$this->startPermission = $startPermission;

		foreach($prizes as $itemString){
			$this->itemPrizes[] = Item::get(...explode(':', $itemString));
		}
	}

	abstract public function initGame(): void;

	abstract public function resetGame(): void;

	abstract public function getExample(): string;

	public function getAnnounceMessage(Main $plugin): string{
		$msg = TF::GREEN.TF::BOLD.$plugin->getMessage('general.game.startHeader', TF::GOLD.$this->getName().TF::GREEN).TF::RESET."\n";
		$msg .= TF::GOLD.$plugin->getMessage('general.game.calculation', TF::AQUA.$this->getCalculation().TF::GOLD).TF::RESET."\n";
		$msg .= TF::GOLD.$plugin->getMessage('general.game.example', TF::DARK_GRAY.$this->getExample().TF::GOLD).TF::RESET."\n";
		$msg .= TF::GOLD.$plugin->getMessage('general.game.howTo').TF::RESET;

		return $msg;
	}

	public function announceGame(Main $plugin, array $recipients = null): void{
		$msg = $this->getAnnounceMessage($plugin);

		$sound = new ClickSound(new Vector3());

		foreach($recipients ?? $plugin->getServer()->getOnlinePlayers() as $player){
			$sound->x = $player->z;
			$sound->y = $player->z;
			$sound->z = $player->z;

			$player->getLevel()->addSound($sound, [$player]);
			$player->addTitle(TF::YELLOW.'GuessTheNumber', TF::GOLD.$this->getName(), 10, 40, 20);
			$player->sendMessage($msg);
		}
	}

	final public function getName(): string{
		return $this->gameName;
	}

	final public function getPlayPermission(): string{
		return $this->playPermission;
	}

	final public function getStartPermission(): string{
		return $this->startPermission;
	}

	public function getFirstNumber(): int{
		return $this->firstInt;
	}

	public function getSecondNumber(): int{
		return $this->secondInt;
	}

	public function getFirstMin(): int{
		return $this->firstMin;
	}

	public function getFirstMax(): int{
		return $this->firstMax;
	}

	public function getSecondMin(): int{
		return $this->secondMin;
	}

	public function getSecondMax(): int{
		return $this->secondMax;
	}

	public function getItemPrizes(): array{
		return $this->itemPrizes;
	}

	public function getCalculation(): string{
		return $this->calculation;
	}

	public function getSolution(): string{
		return (string) $this->solution;
	}

	public function isValidAnswer(string $answer, string $decimalMark, string $thousandSeparator): bool{
		return is_numeric($msg = str_replace([$decimalMark, $thousandSeparator], ['.', ''], $answer));
	}

	public function checkAnswer(string $answer, Player $player, Main $plugin): bool{
		if($this->isSolution($answer)){
			$plugin->getServer()->getPluginManager()->callEvent($event = new PlayerWinEvent($plugin, $this, $player, $answer));

			if(!$event->isCancelled()){
				$this->broadcastWinner($player, $answer, $plugin);
				$this->givePrizes($player, $plugin);
				$plugin->stopGame();
				return true;
			}
		}

		$plugin->getServer()->getPluginManager()->callEvent(new PlayerFailEvent($plugin, $this, $player, $answer));

		$player->sendMessage(TF::RED.$plugin->getMessage('answer.wrong'));
		$player->getLevel()->addSound(new AnvilFallSound($player), [$player]);

		return false;
	}

	protected function broadcastWinner(Player $player, string $answer, Main $plugin): void{
		$msg = TF::GREEN.$plugin->getMessage('answer.right', $player->getDisplayName(), $answer);

		$sound = new FizzSound(new Vector3());

		foreach($plugin->getServer()->getOnlinePlayers() as $p){
			$sound->x = $p->x;
			$sound->y = $p->y;
			$sound->z = $p->z;

			$p->sendMessage($msg);
			$p->getLevel()->addSound($sound, [$p]);
		}
	}

	public function isSolution(string $answer): bool{
		return ((string) $this->solution) == ((string) $answer);
	}

	public function setItemPrizes(array $itemPrizes): void{
		$this->itemPrizes = $itemPrizes;
	}

	public function givePrizes(Player $player, Main $plugin): void{
		$prizeListMessage = TF::GREEN.TF::BOLD.$plugin->getMessage('prizeList.header').TF::RESET;

		foreach($this->itemPrizes as $itemPrize){
			$prizeListMessage .= "\n".TF::AQUA.$plugin->getMessage('prizeList.item', TF::GREEN.$itemPrize->getName().TF::AQUA, TF::LIGHT_PURPLE.$itemPrize->getCount().TF::AQUA).TF::RESET;

			$player->getInventory()->addItem(clone $itemPrize);
		}

		$player->sendMessage($prizeListMessage);
	}
}
