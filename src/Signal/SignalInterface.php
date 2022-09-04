<?php

declare(strict_types=1);
/**
 * This file is part of command-signals.
 *
 * @link     https://github.com/friendsofhyperf/command-signals
 * @document https://github.com/friendsofhyperf/command-signals/blob/main/README.md
 * @contact  huangdijia@gmail.com
 */
namespace FriendsOfHyperf\CommandSignals;

interface SignalInterface
{
    public function wait(int $signo, float $timeout = -1): bool;
}
