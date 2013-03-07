<?php

App::uses('Shell', 'Console');

class AppShell extends Shell {

/**
 * Convenience method for out() that encloses message between <info /> tag
 */
	public function info($message = null, $newlines = 1, $level = Shell::NORMAL) {
		$this->out('<info>' . $message . '</info>', $newlines, $level);
	}

/**
 * Convenience method for out() that encloses message between <warning /> tag
 */
	public function warn($message = null, $newlines = 1, $level = Shell::NORMAL) {
		$this->out('<warning>' . $message . '</warning>', $newlines, $level);
	}

/**
 * Convenience method for out() that encloses message between <success /> tag
 */
	public function success($message = null, $newlines = 1, $level = Shell::NORMAL) {
		$this->out('<success>' . $message . '</success>', $newlines, $level);
	}

}