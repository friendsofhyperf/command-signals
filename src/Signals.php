<?php

declare(strict_types=1);
/**
 * This file is part of command-signals.
 *
 * @link     https://code.addcn.com/friendsofhyperf/command-signals
 * @document https://code.addcn.com/friendsofhyperf/command-signals/blob/main/README.md
 * @contact  greezen@addcn.com
 */
namespace FriendsOfHyperf\CommandSignals;

use Hyperf\Utils\Coroutine;
use Swoole\Coroutine as Co;

class Signals
{
    protected bool $unregistered = false;

    protected array $handlers = [];

    /**
     * @var int[]
     */
    protected array $waits = [];

    public function register(int|array $signo, callable $callback): void
    {
        if (is_array($signo)) {
            array_map(fn ($s) => $this->register((int) $s, $callback), $signo);
            return;
        }

        if (! isset($this->handlers[$signo])) {
            $this->handlers[$signo] = [];
        }

        $this->handlers[$signo][] = $callback;

        $this->wait($signo);
    }

    public function unregister(): void
    {
        $this->unregistered = true;
    }

    protected function wait(int $signo): void
    {
        if (isset($this->waits[$signo])) {
            return;
        }

        $this->waits[$signo] = Coroutine::create(function () use ($signo) {
            defer(fn () => posix_kill(posix_getpid(), $signo));

            while (true) {
                if (Co::waitSignal($signo, 1)) {
                    $callbacks = array_map(fn ($callback) => fn () => $callback($signo), $this->handlers[$signo]);

                    return parallel($callbacks);
                }

                if ($this->unregistered) {
                    break;
                }
            }
        });
    }
}
