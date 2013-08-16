<?php

namespace Denizen\OAuth2;

use \Peyote\Facade as Peyote;

class Client extends \Peyote\PDO implements \League\OAuth2\Server\Storage\ClientInterface
{
	/**
	 * Validate a client
	 *
	 * @param  string     $clientId     The client's ID
	 * @param  string     $clientSecret The client's secret (default = "null")
	 * @param  string     $redirectUri  The client's redirect URI (default = "null")
	 * @param  string     $grantType    The grant type used in the request (default = "null")
	 * @return bool|array               Returns false if the validation fails, array on success
	 */
	public function getClient($clientId, $clientSecret = null, $redirectUri = null, $grantType = null)
	{
		$query = Peyote::select('oauth_clients')
			->where('oauth_clients.id', '=', $clientId);

		$columns = array('oauth_clients.id', 'oauth_clients.secret', 'oauth_clients.name');

		if ($redirectUri !== null)
		{
			$columns[] = 'oauth_client_endpoints.redirect_uri';

			$query->join('oauth_client_endpoints', 'LEFT')->on('oauth_client_endpoints.client_id', '=', 'oauth_clients.id');
			$query->where('oauth_client_endpoints.redirect_uri', '=', $redirectUri);
		}

		if ($clientSecret !== null)
		{
			$query->where('oauth_clients.secret', '=', $clientSecret);
		}

		$query->columnsArray($columns);

		$result = $this->runQuery($query);
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row);
		{
			return false;
		}

		return array(
			'client_id' => $row['id'],
			'client_secret' => $row['secret'],
			'redirect_uri' => isset($row['redirect_uri']) ? $row['redirect_uri'] : null,
			'name' => $row['name'],
		);
	}

}
