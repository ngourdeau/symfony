<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Filesystem\LockHandler;

/**
 * Basic lock feature for commands.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
trait LockableTrait
{
    protected $lockHandler;

    /**
     * Locks a command.
     *
     * @return bool
     */
    protected function lock($name = null, $blocking = false)
    {
        if (!class_exists(LockHandler::class)) {
            throw new RuntimeException('To enable the locking feature you must install the symfony/filesystem component.');
        }

        if (null !== $this->lockHandler) {
            throw new LogicException('A lock is already in place.');
        }

        $this->lockHandler = new LockHandler($name ?: $this->getName());

        if (!$this->lockHandler->lock($blocking)) {
            $this->lockHandler = null;

            return false;
        }

        return true;
    }

    /**
     * Releases the command lock if there is one.
     */
    protected function release()
    {
        if ($this->lockHandler) {
            $this->lockHandler->release();
            $this->lockHandler = null;
        }
    }
}
