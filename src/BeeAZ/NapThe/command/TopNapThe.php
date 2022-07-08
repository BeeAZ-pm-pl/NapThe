<?php

namespace BeeAZ\NapThe\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use BeeAZ\NapThe\Main;
use jojoe77777\FormAPI\SimpleForm;

class TopNapThe extends Command{
  
  private $plugin;
  
  public function __construct($plugin){
         $this->plugin = $plugin;
         parent::__construct("topnapthe", "Top Nạp Thẻ", null);
         $this->setPermission("napthe.command");
        }
  
  public function execute(CommandSender $player, string $label, array $args){
         if($player instanceof Player){
         if($this->testPermission($player, "napthe.command")){
         $this->sendForm($player, $this->plugin->top());
         }
      }
  }
  
  public function sendForm($player, $top){
         $form = new SimpleForm(function($player, $data){
         if($data === null){
         return true;
         }
         });
         $form->setTitle("§d§lTop Nạp Thẻ");
         $form->setContent($top);
         $player->sendForm($form);
         }
}