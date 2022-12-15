<?php

include_once './service/MAEQBotService.php';

$route = $argv[1] ?? null;

$maqeBot = new MAEQBotService();

try {
    $maqeBot->walking(route: $route);

    $position = $maqeBot->getPosition();
    $direction = $maqeBot->getDirection();

    echo sprintf('X: %d Y: %d Direction: %s', $position['x'], $position['y'], $direction);
} catch (Exception $e) {
    echo $e->getMessage();
}

echo "\n";
exit;
