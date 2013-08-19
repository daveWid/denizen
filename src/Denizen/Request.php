<?php

namespace Denizen;

class Request extends \League\OAuth2\Server\Util\Request
{
	/**
	 * {@inheritDoc}
	 */
	public function readHeaders()
	{
		$normalized = array();
		foreach (parent::readHeaders() as $key => $value)
		{
			$normalized[$this->normalizeKey($key)] = $value;
		}

		return $normalized;
	}

	/**
	 * Transform header name into canonical form
	 *
	 * Ripped from the Slim codebase...
	 *
	 * @param  string $key
	 * @return string
	 */
	protected function normalizeKey($key)
	{
		$key = strtolower($key);
		$key = str_replace(array('-', '_'), ' ', $key);
		$key = preg_replace('#^http #', '', $key);
		$key = ucwords($key);
		$key = str_replace(' ', '-', $key);

		return $key;
	}

}
