<?php

namespace Denizen\OAuth2;

class Scope extends \Peyote\PDO implements \League\OAuth2\Server\Storage\ScopeInterface
{
	/**
	 * Return information about a scope
	 *
	 * @param  string     $scope     The scope
	 * @param  string     $clientId  The client ID (default = "null")
	 * @param  string     $grantType The grant type used in the request (default = "null")
	 * @return bool|array If the scope doesn't exist return false
	 */
	public function getScope($scope, $clientId = null, $grantType = null)
	{
		$query = 'SELECT * FROM oauth_scopes WHERE oauth_scopes.scope = ?';
		
		$result = $this->run($query, array($scope));
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row)
		{
			return false;
		}

		return array(
			'id' => (int) $row['id'],
			'scope' => $row['scope'],
			'name' => $row['name'],
			'description' => $row['description']
		);
	}

}
