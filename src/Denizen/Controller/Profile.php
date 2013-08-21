<?php

namespace Denizen\Controller;

class Profile extends API
{
	protected $client;
	protected $user;
	protected $table;

	/**
	 * Make sure we have a valid access token.
	 */
	public function init()
	{
		$this->checkToken();

		$server = $this->app->getResourceServer();

		$this->client = $server->getClientId();
		$this->table = $this->app->table('\\Denizen\\Table\\Users');

		$this->user = $this->table->get($server->getOwnerId());
	}

	public function fetch()
	{
		$this->sendJSON(array('user' => $this->user->toArray()));
	}

	public function update()
	{
		$this->user->set($this->app->request->put());

		$this->validate($this->user);
		$this->table->save($this->user);

		$this->sendJSON(array('user' => $this->user->toArray()), 200);
	}

}
