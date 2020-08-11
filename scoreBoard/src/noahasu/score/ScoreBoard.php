<?php
namespace noahasu\score;

use noahasu\score\Main;
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\PluginBase;
use pocketmine\level\Level;

use pocketmine\scheduler\PluginTask;
use pocketmine\scheduler\Task;
use pocketmine\scheduler\CallBackTask;

use pocketmine\network\mcpe\protocol\SetDisplayObjectivePacket;
use pocketmine\network\mcpe\protocol\SetScorePacket;
use pocketmine\network\mcpe\protocol\types\ScorePacketEntry;
use pocketmine\network\mcpe\protocol\RemoveObjectivePacket;

use Deceitya\MiningLevel\MiningLevelAPI;
use onebone\economyapi\EconomyAPI;

class ScoreBoard extends Task {

    public function __construct(Main $base) {
        $this ->base = $base;
    }

    public function onRun($tick) {
        foreach($this ->base -> getServer() -> getOnlinePlayers() as $player) {
            $name = $player -> getName();
            $money = EconomyAPI::getInstance()->getMonetaryUnit().EconomyAPI::getInstance()->myMoney($name);
            $h = date("G");
            $m = date("i");
            $s = date("s");
            $xyz = "{$player ->getfloorX()},{$player ->getfloorY()},{$player ->getfloorZ()}";
            $world = $player -> getLevel() -> getName();
            $id = "{$player -> getInventory() -> getItemInHand() -> getId()}:{$player -> getInventory() -> getItemInHand() -> getDamage()}";
            $online = count($this ->base -> getServer() -> getOnlinePlayers())."/".$player -> getServer() -> getMaxPlayers();
            $milv = MiningLevelAPI::getInstance() -> getLevel($player);
            $nowex = MiningLevelAPI::getInstance() -> getExp($player);
            $upex = MiningLevelAPI::getInstance() -> getLevelUpExp($player);
            $exp = $upex - $nowex;
            $this->reScoreBoard($player);
            $this->createScoreBoard($player);
            foreach($this ->base ->scoreText -> getAll() as $text => $num) {
                $text = str_replace('%level',$milv,$text);
                $text = str_replace('%upexp',$exp,$text);
                $text = str_replace('%money',$money,$text);
                $text = str_replace('%h',$h,$text);
                $text = str_replace('%m',$m,$text);
                $text = str_replace('%s',$s,$text);
                $text = str_replace('%world',$world,$text);
                $text = str_replace('%id',$id,$text);
                $text = str_replace('%xyz',$xyz,$text);
                $text = str_replace('%online',$online,$text);
                $this -> sendScoreBoard($player,$text,$num);
            }
        }
    }

    public function sendScoreBoard(Player $player,$text,$id) {
        $entpk = new ScorePacketEntry();
        $entpk ->objectiveName = "noahasuScore";
        $entpk ->type = $entpk::TYPE_FAKE_PLAYER;
        $entpk ->customName = $text;
        $entpk ->score = $id;
        $entpk ->scoreboardId = $id+98;
        $pk = new SetScorePacket();
        $pk ->type = $pk::TYPE_CHANGE;
        $pk ->entries[] = $entpk;
        $player -> sendDataPacket($pk);
    }

    public function createScoreBoard(Player $player) {
        $pk = new SetDisplayObjectivePacket();
        $pk ->displaySlot = "sidebar";
        $pk ->objectiveName = "noahasuScore";
        $pk ->displayName = "§b{$player -> getName()}";
        $pk ->criteriaName = "dummy";
        $pk ->sortOrder = 0;
        $player -> sendDataPacket($pk);
    }

    public function reScoreBoard(Player $player) {
        $pk = new RemoveObjectivePacket();
        $pk ->objectiveName = "noahasuScore";
        $player -> sendDataPacket($pk);
    }
}