#!/usr/bin/env php
<?php

/**
 * Hue Nini light - Gradually turns off the lights
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


if ($argc !== 4) {
    echo $argv[0] . " minuites-to-lights-out hue-ip hue-password\n";
    exit(1);
}

require_once __DIR__ . '/vendor/autoload.php';

$client = new \Phue\Client($argv[2], $argv[3]);

$roughTargetTime = $argv[1] * 60; // sec
$variance = 20;
$commands = [];

foreach ($client->getLights() as $light) {
    $command = new \Phue\Command\SetLightState($light);
    $command->brightness(0)
        ->colorTemp(500)
        ->on(false);

    $transitionTime = (int)($roughTargetTime * (1 + (rand(0 - $variance / 2, $variance / 2)) / 100));

    echo "$transitionTime \n";
    $command->transitionTime($transitionTime);
    $commands[] = $command;

    $scheduleCommand = new \Phue\Command\CreateSchedule(
        'Dim all lights',
        "+$transitionTime seconds",
        (new \Phue\Command\SetLightState($light))->on(false)
    );

    $commands[] = $scheduleCommand;
}

// Send the command
foreach ($commands as $i => $command) {
    echo "Starting command $i \n";
    $client->sendCommand(
        $command
    );
}
