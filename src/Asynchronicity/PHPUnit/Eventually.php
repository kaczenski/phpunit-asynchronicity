<?php
declare(strict_types=1);

namespace Asynchronicity\PHPUnit;

use Asynchronicity\Polling\Interrupted;
use Asynchronicity\Polling\Poller;
use Asynchronicity\Polling\SystemClock;
use Asynchronicity\Polling\Timeout;
use PHPUnit\Framework\Constraint\Constraint;

final class Eventually extends Constraint
{
    private $timeoutMilliseconds;
    private $waitMilliseconds;

    public function __construct(int $timeoutMilliseconds = 5000, int $waitMilliseconds = 500)
    {
        $this->timeoutMilliseconds = $timeoutMilliseconds;
        $this->waitMilliseconds = $waitMilliseconds;
    }

    public function evaluate($probe, string $description = '', bool $returnResult = false): bool
    {
        try {
            $poller = new Poller();
            $poller->poll(
                $probe,
                new Timeout(new SystemClock(), $this->waitMilliseconds, $this->timeoutMilliseconds)
            );
        } catch (Interrupted $exception) {
            if ($returnResult) {
                return false;
            }

            throw $exception;
        }

        return true;
    }

    protected function failureDescription($other): string
    {
        return 'the given probe was satisfied within the provided timeout';
    }

    public function toString(): string
    {
        throw new \BadMethodCallException('Not implemented');
    }
}
