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

use Hyperf\Engine\Signal;
use Hyperf\Utils\Coroutine;

class Signals
{
    protected array $handlers = [];

    /**
     * @var int[]
     */
    protected array $registered = [];

    protected bool $unregistered = false;

    public function __construct(protected int $concurrent = 0)
    {
    }

    public function register(int|array $signo, callable $callback): void
    {
        if (is_array($signo)) {
            array_map(fn ($s) => $this->register((int) $s, $callback), $signo);
            return;
        }

        $this->handlers[$signo] ??= [];
        $this->handlers[$signo][] = $callback;
        $this->wait($signo);
    }

    public function unregister(): void
    {
        $this->unregistered = true;
    }

    protected function wait(int $signo): void
    {
        if (isset($this->registered[$signo])) {
            return;
        }

        $this->registered[$signo] = Coroutine::create(function () use ($signo) {
            defer(fn () => posix_kill(posix_getpid(), $signo));

            while (true) {
                if (Signal::wait($signo, 1)) {
                    $callbacks = array_map(fn ($callback) => fn () => $callback($signo), $this->handlers[$signo]);

                    return parallel($callbacks, $this->concurrent);
                }

                if ($this->unregistered) {
                    break;
                }
            }
        });
    }
}
