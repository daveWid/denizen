<?php

namespace Denizen;

use \League\OAuth2\Server\Authorization,
	\League\OAuth2\Server\Resource,
	\League\OAuth2\Server\Grant\ClientCredentials,
	\League\OAuth2\Server\Grant\Password;

class Application extends \Slim\Slim
{
	private $pdo = null;
	private $serverAuth = null;
	private $serverResource = null;

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
	 * @return \PDO
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

	public function getAuthServer()
	{
		if ($this->serverAuth === null)
		{
			$this->serverAuth = new Authorization(
				new \Denizen\OAuth2\Client($this->getPDO()),
				new \Denizen\OAuth2\Session($this->getPDO()),
				new \Denizen\OAuth2\Scope($this->getPDO())
			);

			$this->serverAuth->setRequest(\Denizen\Request::buildFromSlim($this->request));

			$this->serverAuth->addGrantType(new Password($this->serverAuth));
			$this->serverAuth->addGrantType(new ClientCredentials($this->serverAuth));
		}

		return $this->serverAuth;
	}

	public function getResourceServer()
	{
		if ($this->serverResource === null)
		{
			$this->serverResource = new Resource(
				new \Denizen\OAuth2\Session($this->getPDO())
			);

			$this->serverResource->setRequest(\Denizen\Request::buildFromSlim($this->request));
		}

		return $this->serverResource;
	}

	/**
	 * Creates a new controller while injecting the app into it.
	 *
	 * @param  string $name The name of the controller
	 * @return mixed        The controller with the app injected
	 */
	public function controller($name)
	{
		return new $name($this);
	}

	/**
	 * Creates an instance of a \Peyote\PDO table class with the PDO injected.
	 *
	 * @param  string $name  Name of the class to create
	 * @return mixed         The class with the pdo instance injected
	 */
	public function table($name)
	{
		return new $name($this->getPDO());
	}

}
