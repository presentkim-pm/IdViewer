<?php
declare(strict_types=1);

namespace kim\present\idviewer;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

final class IdViewer extends PluginBase implements Listener{
    protected function onEnable() : void{
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    protected function onDisable() : void{
        $this->getConfig()->save();
    }

    public function onPlayerInteract(PlayerInteractEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->isViewer($player))
            return;

        $block = $event->getBlock();
        $item = $block->asItem();
        $player->sendPopup(TextFormat::AQUA . "{$block->getName()} ({$block->getId()}:{$block->getMeta()}) / asItem({$item->getId()}:{$item->getMeta()})");
    }

    public function PlayerItemHeld(PlayerItemHeldEvent $event) : void{
        $player = $event->getPlayer();
        if(!$this->isViewer($player))
            return;

        $item = $event->getItem();
        $player->sendPopup(TextFormat::AQUA . "{$item->getName()} ({$item->getId()}:{$item->getMeta()})");
    }

    /** @param string[] $args */
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if(!$sender instanceof Player){
            $sender->sendMessage(TextFormat::RED . "It can only be used in-game");
            return true;
        }

        if($this->isViewer($sender)){
            $this->removeViewer($sender);
            $sender->sendMessage(TextFormat::AQUA . "[IdViewer] Disable id viewer");
        }else{
            $this->addViewer($sender);
            $sender->sendMessage(TextFormat::AQUA . "[IdViewer] Enable id viewer");
        }
        return true;
    }

    public function isViewer(Player $player) : bool{
        return (bool) $this->getConfig()->get($player->getXuid(), false);
    }

    public function addViewer(Player $player) : void{
        $this->getConfig()->set($player->getXuid(), true);
    }

    public function removeViewer(Player $player) : void{
        $this->getConfig()->remove($player->getXuid());
    }
}