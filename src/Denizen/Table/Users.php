<?php

namespace Denizen\Table;

use \Peyote\Facade as Peyote;

class Users extends \Peyote\PDO
{
	private $table = 'users';
	private $id_property = 'users_id';

	/**
	 * Fetch a user by email.
	 *
	 * @param  string $email The email address to find
	 * @return mixed         An array or user data or null
	 */
	public function fetchByEmail($email)
	{
		$query = Peyote::select($this->table)
			->where('email', '=', $email)
			->limit(1);

		$result = $this->runQuery($query);
		return $result->fetch(\PDO::FETCH_ASSOC);
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

		if ( ! password_verify($password, $user['password']))
		{
			return array();
		}

		return $this->convert($user);
	}

	/**
	 * Do some data conversion on a single row.
	 *
	 * @param  array  $data The data to convert
	 * @return array
	 */
	public function convert(array $data)
	{
		unset($data['password']);
		$data['user_id'] = (int) $data['user_id'];

		return $data;
	}

}
