#!/usr/bin/env php
<?php

/**
 * Hue Christmas Light - Gradually turns off the lights
 * Copyright (C) 2016  Billie Alice Thompson <billie@purplebooth.co.uk>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 **/


use Phue\Command\CreateSchedule;
use Phue\Command\SetLightState;
use Phue\TimePattern\Timer;

if ($argc !== 4) {
    echo $argv[0] . " hue-ip hue-password lights-to-use-regex\n";
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

$client = new \Phue\Client($argv[1], $argv[2]);

$colors = [
    'red' => ['x' => 0.3972, 'y' => 0.4564],
    'yellow' => ['x' => 0.5425, 'y' => 0.4196],
    'green' => ['x' => 0.41, 'y' => 0.51721],
    'blue' => ['x' => 0.1691, 'y' => 0.0441],
    'pink' => ['x' => 0.4149, 'y' => 0.1776],
];

do {
    $sleepTime = 0;

    foreach ($client->getLights() as $i => $light) {
        $command = new SetLightState($light);
        if (!$light->isOn()) {
            continue;
        }

        if (preg_match($argv[3], $light->getName()) === 0) {
            continue;
        }

        $colorKey = array_keys($colors)[rand(0, count($colors) - 1)];
        $color = $colors[$colorKey];

        $command->xy($color['x'], $color['y']);
        $command->brightness(SetLightState::BRIGHTNESS_MAX / 2);

        $transitionTime = rand(1, 5);
        $command->transitionTime($transitionTime);
        $delayTime = rand(0, 10 - $transitionTime);

        if ($delayTime > 0) {
            $delayCommand = new CreateSchedule(
                'Christmas Light Delay',
                new Timer($delayTime),
                $command
            );

            $delayCommand->send($client);
        } else {
            $command->send($client);
        }

        printf("Changing Light %s to %s in %d seconds\n", $light->getName(), $colorKey, $delayTime);

        $sleepTime = max($transitionTime + $delayTime, $sleepTime);
    }

    printf("Waiting %d seconds to reset loop", $sleepTime);
    sleep($sleepTime);
} while (true);
