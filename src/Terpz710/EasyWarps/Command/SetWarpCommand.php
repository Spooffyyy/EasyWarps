<?php

declare(strict_types=1);

namespace Terpz710\EasyWarps\Command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;
use Terpz710\EasyWarps\Main;

class SetWarpCommand extends Command {

    private $plugin;

    public function __construct(Plugin $plugin) {
        parent::__construct("setwarp", "Set a warp point in your current world", "/setwarp <warpName> <visibility: op or true>");
        $this->plugin = $plugin;
        $this->setPermission("easywarp.setwarp");
    }

    public function execute(CommandSender $sender, string $label, array $args): bool {
        if ($sender instanceof Player) {
            if (count($args) === 2) {
                $warpName = strtolower($args[0]);
                $visibility = strtolower($args[1]);

                if ($visibility === "op" || $visibility === "true") {
                    // Generate a permission node based on the warp name
                    $warpPermission = "easywarp.warp.$warpName";

                    $position = $sender->getPosition(); // Get the player's position.

                    $warpLocation = [
                        'x' => $position->getX(),
                        'y' => $position->getY(),
                        'z' => $position->getZ(),
                        'world' => $sender->getWorld()->getFolderName(),
                        'permission' => $warpPermission,
                        'visibility' => $visibility,
                    ];

                    $this->saveGlobalWarpData($warpName, $warpLocation, $warpPermission, $visibility); // Pass $warpPermission and $visibility as arguments

                    $sender->sendMessage(TextFormat::GREEN . "Global warp point '$warpName' set! Visibility: $visibility");
                    return true;
                } else {
                    $sender->sendMessage(TextFormat::RED . "Invalid visibility parameter. Use 'op' or 'true'.");
                }
            } else {
                $sender->sendMessage(TextFormat::RED . "Usage: /setwarp <warpName> <visibility: op or true>");
            }
        } else {
            $sender->sendMessage(TextFormat::RED . "You must be a player to use this command.");
        }
        return false;
    }

    private function saveGlobalWarpData(string $warpName, array $warpLocation, string $warpPermission, string $visibility): void { // Accept $warpPermission and $visibility as arguments
        $config = new Config($this->plugin->getDataFolder() . "GlobalWarps.yml", Config::YAML);

        if (!$config->exists("warps")) {
            $config->set("warps", []);
        }

        $warps = $config->get("warps");
        $warps[$warpName] = $warpLocation;

        $config->set("warps", $warps);

        // Dynamically create the permission node based on visibility settings
        if ($visibility === "op") {
            // Create a permission node with "op"
            $this->plugin->getServer()->getPluginManager()->addPermission($warpPermission, null, "op");
        } else {
            // Create a permission node with "true"
            $this->plugin->getServer()->getPluginManager()->addPermission($warpPermission, null, "true");
        }

        $config->save();
    }
}
