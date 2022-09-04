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

class SwowSignal implements SignalInterface
{
    public function wait(int $signo, float $timeout = -1): bool
    {
        try {
            /** @phpstan-ignore-next-line */
            $result = \Swow\Signal::wait($signo, (int) ($timeout * 1000));
        } catch (\Throwable $exception) {
            $result = false;
        }

        return true;
    }
}
