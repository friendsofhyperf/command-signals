<?php

declare(strict_types=1);
/**
 * This file is part of command-signals.
 *
 * @link     https://code.addcn.com/friendsofhyperf/command-signals
 * @document https://code.addcn.com/friendsofhyperf/command-signals/blob/main/README.md
 * @contact  greezen@addcn.com
 */
namespace FriendsOfHyperf\CommandSignals\Traits;

use FriendsOfHyperf\CommandSignals\Signals;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use TypeError;

trait InteractsWithSignals
{
    protected ?Signals $signals = null;

    /**
     * @param int|int[] $signo
     * @throws TypeError
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     */
    protected function trap(array|int $signo, callable $callback): void
    {
        if (! $this->signals) {
            $this->signals = new Signals();
            defer(fn () => $this->signals->unregister());
        }

        $this->signals->register($signo, $callback);
    }
}
