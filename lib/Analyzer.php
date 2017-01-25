<?php

namespace Gizmo;

use \JsonStreamingParser\Parser;
use \Gizmo\ReplayListener;

class Analyzer
{
	public function __construct() {}

	/**
	 * @param string $path
	 */
	public function analyze($path)
	{
		$stream = fopen($path, 'r');
		$listener = new ReplayListener();
		try {
			$parser = new Parser($stream, $listener);
			$parser->parse();
			fclose($stream);
		}
		catch (Exception $e) {
			fclose($stream);
			throw $e;
		}
	}
}
