<?php

namespace Denizen;

class Application extends \Slim\Slim
{
	private $pdo = null;

	/**
	 * Gets the url for the site. Useful for applications that are in subdirectories
	 *
	 * Set <code>site.url</code> when creating the application instance.
	 *
	 * @return string
	 */
	public function getSiteUrl()
	{
		$url = $this->config('site.url', '');
		return \rtrim($url, '/');
	}

	/**
	 * Gets a global PDO instance used for database interactions.
	 *
	 * Set <code>db</code> when creating the application instance.
	 *
	 * @return [type] [description]
	 */
	public function getPDO()
	{
		if ($this->pdo === null)
		{
			$config = $this->config('db');
			$this->pdo = new \PDO($config['dsn'], $config['user'], $config['password']);
		}

		return $this->pdo;
	}

}
