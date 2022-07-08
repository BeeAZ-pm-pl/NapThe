<?php

namespace BeeAZ\NapThe\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use BeeAZ\NapThe\NaptheTask;
use BeeAZ\NapThe\Main;
use jojoe77777\FormAPI\CustomForm;

class NaptheCommand extends Command{
  
  private $plugin;
  
  public function __construct($plugin){
         $this->plugin = $plugin;
         parent::__construct("napthe", "Nạp Thẻ", null);
         $this->setPermission("napthe.command");
        }
  
  public function execute(CommandSender $player, string $label, array $args){
         if($player instanceof Player){
         if($this->testPermission($player, "napthe.command")){
         $this->sendForm($player, $this->plugin->cfg->get("TEXT"));
         }
      }
  }
  public function sendForm($player, $text = null){
       $form = new CustomForm(function($player, $data = null){
       if($data === null){
       return true;
       }
       if(!is_numeric($data[3])){;
       return $this->sendForm($player, "§f§l↣ Mã Thẻ Phải Là Số");
       }
       if(strpos($data[3], ".") !== false){
       return $this->sendForm($player, "§f§l↣ Mã Thẻ Phải Là Số Nguyên");
       }
       if(!is_numeric($data[4])){
       return $this->sendForm($player, "§f§l↣ Seri Phải Là Số");
       }
       if(strpos($data[4], ".") !== false){
       return $this->sendForm($player, "§f§l↣ Seri Phải Là Số Nguyên");
       }
       $telcos = ["Viettel", "Vietnamobile", "Vina", "Mobifone", "Zing", "GATE"];
       $menhgia = ["10000", "20000", "30000", "50000", "100000", "200000", "300000", "500000", "1000000"];
       $this->plugin->getScheduler()->scheduleDelayedTask(new NaptheTask($this->plugin, $player, $telcos[$data[1]], $data[3], $data[4], $menhgia[$data[2]], ((string)time())), 25);
       });
       $form->setTitle("§l§aNạp Thẻ");
       $form->addLabel($text);
       $form->addDropdown("§l§c• §eChọn nhà mạng:", ["Viettel", "Vietnamobile", "Vina", "Mobifone", "Zing", "GATE"]);
       $form->addDropdown("§l§c• §eChọn mệnh giá: (Chọn sai sẽ chỉ nhận được 50% Giá Trị)", ["10000", "20000", "30000", "50000", "100000", "200000", "300000", "500000", "1000000"]);
       $form->addInput("§l§c• §eNhập mã thẻ: ");
       $form->addInput("§l§c• §eNhập seri thẻ: ");
       $player->sendForm($form);
       return $form;
     }
}