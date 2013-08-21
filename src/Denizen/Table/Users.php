<?php

namespace Denizen\Table;

use \Peyote\Facade as Peyote;

class Users extends \Peyote\PDO
{
	/**
	 * Fetch a user by email.
	 *
	 * @param  string $email The email address to find
	 * @return mixed         An array or user data or null
	 */
	public function fetchByEmail($email)
	{
		$query = Peyote::select('users')
			->where('email', '=', $email)
			->limit(1);

		$result = $this->runQuery($query);
		return $this->convert($result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * @param  string $id The id to get
	 * @return mixed      \Denizen\Model\User or null
	 */
	public function get($id)
	{
		$query = Peyote::select('users')
			->columns('id', 'email', 'first_name', 'last_name')
			->where('id', '=', $id);

		$result = $this->runQuery($query);
		return $this->convert($result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * @return \Peyote\Collection
	 */
	public function all()
	{
		$query = Peyote::select('users')
			->columns('id', 'email', 'first_name', 'last_name')
			->orderBy('id', 'ASC')
			->limit(20);

		$result = $this->runQuery($query);

		$self = $this;
		$collection = new \Peyote\Collection;

		foreach ($result->fetchAll(\PDO::FETCH_ASSOC) as $row)
		{
			$collection->addOne($this->convert($row));
		}

		return $collection;
	}

	/**
	 * Saves a user model to the database
	 * @param  \Peyote\Model $model  The model to save
	 * @return [type]        [description]
	 */
	public function save(\Peyote\Model & $model)
	{
		if ($model->isNew())
		{
			$this->create($model);
		}
		else
		{
			$this->update($model);
		}
	}

	/**
	 * Create a new user.
	 *
	 * @param  Peyote\Model $model  The model to create
	 */
	protected function create(\Peyote\Model & $model)
	{
		$data = array(
			'email' => $model->get('email'),
			'password' => password_hash($model->get('password'), \PASSWORD_BCRYPT, array('cost' => 12)),
			'first_name' => $model->get('first_name'),
			'last_name' => $model->get('last_name')
		);

		$id = $this->insert('users', $data);
		$model = $this->get($id);
	}

	/**
	 * Updates data for a user
	 *
	 * @param  Peyote\Model $model  The model to create
	 */
	protected function update(\Peyote\Model & $model)
	{
		$data = $model->getModifiedData();
		$whitelist = array('email', 'first_name', 'last_name');

		$update = \array_intersect_key($data, \array_flip($whitelist));

		if ( ! empty($update))
		{
			$query = Peyote::update('users')->set($update);
			$this->runQuery($query);
		}

		$model = $this->get($model->get('id'));
	}

	/**
	 * @param  int $id   The id to delete
	 * @return int       The number of deleted rows
	 */
	public function delete($id)
	{
		$query = Peyote::delete('users')->where('id', '=', $id);
		$result = $this->runQuery($query);

		return $result->rowCount();
	}

	/**
	 * Attempts to log the user in.
	 *
	 * @param  string $user     The username
	 * @param  string $password The password
	 * @return mixed            An array of user data or null
	 */
	public function login($user, $password)
	{
		$user = $this->fetchByEmail($user);

		if ($user === null)
		{
			return array();
		}

		if ( ! password_verify($password, $user->password))
		{
			return array();
		}

		return $user;
	}

	/**
	 * Do some data conversion on a single row.
	 *
	 * @param  mixed  $data The data to convert
	 * @return array
	 */
	public function convert($data = null)
	{
		if ( ! $data)
		{
			return null;
		}

		$data['id'] = (int) $data['id'];
		$model = new \Denizen\Model\User($data);
		return $model->reset();
	}

}
