<?php

namespace Denizen\Controller;

class Users extends API
{
	protected $client;
	protected $table;

	/**
	 * Make sure we have a valid client_credentials token.
	 */
	public function init()
	{
		$this->checkToken();
		$this->client = $this->app->getResourceServer()->getClientId();
		$this->table = $this->app->table('\\Denizen\\Table\\Users');
	}

	public function getAll()
	{
		$users = $this->table->all();
		$this->sendJSON(array('users' => $users->toArray()));
	}

	public function getOne($id)
	{
		$user = $this->table->get($id);
		$this->sendJSON(array('user' => $user->toArray()));
	}

	public function create()
	{
		$data = $this->app->request->post();
		$user = new \Denizen\Model\NewUser($data);

		$this->validate($user);

		try {
			$this->table->save($user);

			$this->sendJSON(array(
				'user' => $user->toArray()
			), 201);
		} catch(\PDOException $e) {
			// Only error at this point is a duplicate email address...
			$this->sendJSON(array(
				'errors' => array('email|unique')
			), 400);
		}
	}

	public function update($id)
	{
		$user = $this->table->get($id);

		if ($user === null)
		{
			$this->sendJSON(array(
				'error' => "User not found"
			), 404);
		}

		$user->set($this->app->request->put());

		$this->validate($user);
		$this->table->save($user);

		$this->sendJSON(array('user' => $user->toArray()), 200);
	}

	public function delete($id)
	{
		$this->table->delete($id);
		$this->sendJSON(array(), 200);
	}

}
