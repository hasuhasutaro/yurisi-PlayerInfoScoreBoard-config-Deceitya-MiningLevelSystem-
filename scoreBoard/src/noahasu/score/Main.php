<?php
namespace noahasu\score;

use pocketmine\Player;
use pocketmine\Server;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\utils\Config;
use noahasu\score\ScoreBoard;

class Main extends PluginBase implements Listener {
    public $scoreText;
    public function onEnable() {
        $this -> getServer() -> getPluginManager() -> registerEvents($this,$this);
        $this ->scoreText = new Config($this -> getDataFolder()."scoretext.yml",Config::YAML,array(
            '所持金: %money' => 1,
            '現在のmineLevel: %level (次のレベルまで%upexpexp)' => 2,
            '座標: %xyz' => 3,
            'ワールド: %world' => 4,
            '現在の時刻: %h時%m分%s秒' => 5,
            '所持アイテムid: %id' => 6,
            'オンライン人数: %online' => 7
        ));
        $this ->time = new Config($this -> getDataFolder()."time.yml",Config::YAML,array(
            'スコアボードの更新時間(20で1秒)' => 10
        ));
        $this -> getScheduler() -> scheduleRepeatingTask(new ScoreBoard($this),$this ->time -> get('スコアボードの更新時間(20で1秒)'));
        date_default_timezone_set('Asia/Tokyo');
    }
}
