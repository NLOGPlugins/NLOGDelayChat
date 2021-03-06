<?php

namespace nlog\NLOGDelayChat;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\utils\TextFormat;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener{

	public $time, $chat, $count, $config, $delay;
	
 	public function onEnable(){
    	$this->getServer()->getPluginManager()->registerEvents($this, $this);
    	$this->getLogger()->notice("채팅 딜레이 (도배 방지) 플러그인입니다.");
    	$this->getLogger()->notice("Made by NLOG (nlog.kro.kr)");
    	
    	
    	@mkdir($this->getDataFolder());
   	$this->config = new Config($this->getDataFolder() . "setting.yml", Config::YAML);
    	
    	if (!$this->config->exists('Delay-Time')) {
    		$this->config->set('Delay-Time', 3);
    		$this->config->save();
    	}
		
	if (!is_numeric($this->config->get('Delay-Time'))) {
		$this->config->set('Delay-Time', 3);
    		$this->config->save();
	}
    	
    	$this->count = [ ];
    	$this->time = [ ];
    	$this->chat = [ ];
		
	$this->delay = $this->config->get('Delay-Time');
 	}
 	 
 	public function onPlayerChat (PlayerChatEvent $ev) {
 	 	
 	 	$name = strtolower($ev->getPlayer()->getName());
 	 	$msg = md5(strtolower(TextFormat::clean($ev->getMessage())));
		 
		if(!isset($this->chat[$name])){
			$this->chat[$name] = [];
		}
 	 	
 	 	if (isset($this->time[$name])) {
 	 		if (time() - $this->time[$name] <= $this->delay) {
 	 			$ev->setCancelled(true);
 	 			
 	 			$this->time[$name] = time();
 	 			
 	 			if (!isset($this->count[$name])) {
 	 				$this->count[$name] = 1;
 	 			}else{
 	 				$this->count[$name] += 1;
 	 			}
 	 			
 	 			if ($this->count[$name] > 3) {
 	 				$ev->getPlayer()->kick("도배 방지를 위해 킥되었습니다.");
 	 				return true;
 	 			}
 	 			
 	 			$ev->getPlayer()->sendMessage("§o§b[ 알림 ]§7 ".$this->delay."초 뒤에 다시 입력해주세요.");
 	 			return true;
 	 		}
		}
		
 	 	if (isset($this->chat[$name][$msg])) {
 	 		$ev->setCancelled(true);
 	 		$ev->getPlayer()->sendMessage("§o§b[ 알림 ]§7 이미 입력하신 채팅 내용입니다.");
 	 		return true;
 	 	}
		
		if (isset($this->count[$name])) {
 	 		unset($this->count[$name]);
 	 	}
		
 	 	$this->chat[$name][$msg] = "";
		$this->time[$name] = time();
 	 }
 	 
 	 public function onPlayerQuitEvent (PlayerQuitEvent $ev) {
 	 	
 	 	$name = strtolower($ev->getPlayer()->getName());
 	 	
 	 	if (isset($this->chat[$name])) {
 	 		unset($this->chat[$name]);
 	 	}
 	 	if (isset($this->time[$name])) {
 	 		unset($this->time[$name]);
 	 	}
 	 	if (isset($this->count[$name])) {
 	 		unset($this->count[$name]);
 	 	}
 	 }
 	 
 	 
  }
?>
