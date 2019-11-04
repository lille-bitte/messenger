<?php

namespace LilleBitte\Messenger;

/**
 * @author Paulus Gandung Prakosa <rvn.plvhx@gmail.com>
 */
trait ResponseTrait
{
	private function ensureHasContentType(array $headers)
	{
		foreach ($headers as $key => $value) {
			if (strtolower($key) === 'content-type') {
				return true;
			}
		}

		return false;
	}
}
