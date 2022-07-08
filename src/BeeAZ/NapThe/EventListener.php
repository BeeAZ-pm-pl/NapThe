<?php

namespace BeeAZ\NapThe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;

class EventListener implements Listener{
  
  
  public $plugin;
  
  public function __construct($plugin){
         $this->plugin = $plugin;
  }
  
  public function onJoin(PlayerJoinEvent $event){
         $player = $event->getPlayer();
         $name = strtolower($player->getName());
         $sql = $this->plugin->db->prepare("SELECT * FROM dulieu WHERE donater = :name");
         $sql->bindValue(":name", $name);
         $result = $sql->execute();
         if($result->fetchArray(SQLITE3_ASSOC) == null){
         $sql = $this->plugin->db->prepare("INSERT INTO dulieu(donater) VALUES (:name);");
         $sql->bindValue(":name", $name);
         $sql->execute();
         }
    }
}
