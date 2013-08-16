<?php

namespace Denizen\Controller;

class Users extends API
{
	protected $client;

	/**
	 * Make sure we have a valid client_credentials token.
	 */
	public function init()
	{
		$this->checkToken();
		$this->client = $this->app->getResourceServer()->getClientId();
	}

	public function getAll()
	{
		var_dump($this->client);
	}

	public function getOne()
	{

	}

	public function create()
	{

	}

	public function update()
	{

	}

	public function delete()
	{

	}

}
