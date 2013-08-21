<?php

namespace Denizen\Table;

use \Peyote\Facade as Peyote;

class PasswordTokens extends \Peyote\PDO
{
	/**
	 * @param  int $id  The token id
	 * @return mixed    array of data or null
	 */
	public function get($id)
	{
		$query = Peyote::select('password_tokens')
			->where('id', '=', $id);

		$result = $this->runQuery($query);
		return $this->convert($result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * @param  string $token The password token
	 * @return mixed         array of data or null
	 */
	public function fetchByToken($token)
	{
		$query = Peyote::select('password_tokens')
			->where('password_token', '=', $token)
			->where('password_token_expires', '>', time())
			->limit(1);

		$result = $this->runQuery($query);
		return $this->convert($result->fetch(\PDO::FETCH_ASSOC));
	}

	/**
	 * Create a new token with the given id
	 *
	 * @param  int $id  The user id
	 * @return array
	 */
	public function create($id)
	{
		$expire = 3600;

		$data = array(
			'user_id' => $id,
			'password_token' => \League\OAuth2\Server\Util\SecureKey::make(24),
			'password_token_expires' => time() + $expire
		);

		$query = Peyote::insert('password_tokens')
			->columns(\array_keys($data))
			->values(\array_values($data));

		$result = $this->runQuery($query);

		$token = $this->get($this->pdo->lastInsertId());
		$token['password_token_expires_in'] = $expire;
		return $token;
	}

	/**
	 * @param  int $id  The token id
	 * @return int      The number of affected rows
	 */
	public function delete($id)
	{
		$query = Peyote::delete('password_tokens')
			->where('id', '=', $id);

		$result = $this->runQuery($query);
		return $result->rowCount();
	}

	protected function convert($data = null)
	{
		if ( ! $data)
		{
			return null;
		}

		$data['id'] = (int) $data['id'];
		$data['user_id'] = (int) $data['user_id'];
		$data['password_token_expires'] = (int) $data['password_token_expires'];

		return $data;
	}

}
