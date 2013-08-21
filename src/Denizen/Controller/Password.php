<?php

namespace Denizen\Controller;

class Password extends API
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
		$this->table = $this->app->table('\\Denizen\\Table\\PasswordTokens');
	}

	public function change()
	{
		$data = $this->app->request->put();

		if ( ! isset($data['password_token']))
		{
			$this->sendJSON(array(
				'errors' => array('password_token|required') 
			), 400);
		}

		$token = $this->table->fetchByToken($data['password_token']);

		if ($token === null)
		{
			$this->sendJSON(array(
				'errors' => array('password_token|valid')
			), 400);
		}

		$user_table = $this->app->table('\\Denizen\\Table\\Users');
		$user = $user_table->get($token['user_id']);

		$user = new \Denizen\Model\NewUser($user->toArray());
		$user->set(array(
			'password' => $data['password'],
			'confirm_password' => $data['confirm_password']
		));

		$errors = $user->validate();
		if ( ! empty($errors))
		{
			$this->sendJSON(array('errors' => $errors), 400);
		}

		$user_table->updatePassword($user);
		$this->table->delete($token['id']);

		$this->sendJSON(array('success' => true), 200);
	}

	public function createToken()
	{
		$email = $this->app->request->post('email');

		$user_table = $this->app->table('\\Denizen\\Table\\Users');
		$user = $user_table->fetchByEmail($email);

		if ($user === null)
		{
			$this->sendJSON(array(
				'errors' => array('email|exists')
			), 400);
		}

		$token = $this->table->create($user->id);
		$this->sendJSON($token, 200);
	}

}
