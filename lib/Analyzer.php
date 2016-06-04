<?php

namespace Gizmo;

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
			$parser = new \JsonStreamingParser\Parser($stream, $listener);
			$parser->parse();
			fclose($stream);
		}
		catch (Exception $e) {
			fclose($stream);
			throw $e;
		}
	}
}
