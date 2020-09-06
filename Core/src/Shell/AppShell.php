<?php

namespace Croogo\Core\Shell;

use Cake\Console\Shell;
use Psr\Log\LogLevel;

/**
 * Base class for Croogo Shell
 *
 * @package Croogo.Console
 */
class AppShell extends Shell
{
    /**
     * Convenience method for out() that encloses message between <info /> tag
     */
    public function info($message, $newlines = 1, $level = Shell::NORMAL): ?int
    {
        return $this->out('<info>' . $message . '</info>', $newlines, $level);
    }

    /**
     * Convenience method for out() that encloses message between <warning /> tag
     */
    public function warn($message, int $newlines = 1): int
    {
        return $this->out('<warning>' . $message . '</warning>', $newlines, Shell::NORMAL);
    }

    /**
     * Convenience method for out() that encloses message between <success /> tag
     */
    public function success($message, int $newlines = 1, int $level = Shell::NORMAL): ?int
    {
        return $this->out('<success>' . $message . '</success>', $newlines, $level);
    }
}
