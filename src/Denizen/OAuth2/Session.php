<?php

namespace Denizen\OAuth2;

use \Peyote\Facade as Peyote;

class Session extends \Peyote\PDO implements \League\OAuth2\Server\Storage\SessionInterface
{
	/**
	 * Create a new session
	 *
	 * @param  string $clientId  The client ID
	 * @param  string $ownerType The type of the session owner (e.g. "user")
	 * @param  string $ownerId   The ID of the session owner (e.g. "123")
	 * @return int               The session ID
	 */
	public function createSession($clientId, $ownerType, $ownerId)
	{
		$data = array(
			'client_id' => $clientId,
			'owner_type' => $ownerType,
			'owner_id' => $ownerId
		);

		return $this->insert('oauth_sessions', $data);
	}

	/**
	 * Delete a session
	 *
	 * @param  string $clientId  The client ID
	 * @param  string $ownerType The type of the session owner (e.g. "user")
	 * @param  string $ownerId   The ID of the session owner (e.g. "123")
	 * @return void
	 */
	public function deleteSession($clientId, $ownerType, $ownerId)
	{
		$query = Peyote::delete('oauth_sessions')
			->where('client_id', '=', $clientId)
			->where('owner_type', '=', $ownerType)
			->where('owner_id', '=', $ownerId);

		$this->runQuery($query);
	}

	/**
	 * Associate a redirect URI with a session
	 *
	 * @param  int    $sessionId   The session ID
	 * @param  string $redirectUri The redirect URI
	 * @return void
	 */
	public function associateRedirectUri($sessionId, $redirectUri)
	{
		$data = array(
			'session_id' => $sessionId,
			'redirect_uri' => $redirectUri
		);

		$this->insert('oauth_session_redirects', $data);
	}

	/**
	 * Associate an access token with a session
	 *
	 * @param  int    $sessionId   The session ID
	 * @param  string $accessToken The access token
	 * @param  int    $expireTime  Unix timestamp of the access token expiry time
	 * @return void
	 */
	public function associateAccessToken($sessionId, $accessToken, $expireTime)
	{
		$data = array(
			'session_id' => $sessionId,
			'access_token' => $accessToken,
			'access_token_expires' => $expireTime
		);

		$this->insert('oauth_session_access_tokens', $data);
	}

	/**
	 * Associate a refresh token with a session
	 *
	 * @param  int    $accessTokenId The access token ID
	 * @param  string $refreshToken  The refresh token
	 * @param  int    $expireTime    Unix timestamp of the refresh token expiry time
	 * @param  string $clientId      The client ID
	 * @return void
	 */
	public function associateRefreshToken($accessTokenId, $refreshToken, $expireTime, $clientId)
	{
		$data = array(
			'session_access_token_id' => $accessTokenId,
			'refresh_token' => $refreshToken,
			'refresh_token_expires' => $expireTime,
			'client_id' => $clientId
		);

		$this->insert('oauth_session_access_tokens', $data);
	}

	/**
	 * Assocate an authorization code with a session
	 *
	 * @param  int    $sessionId  The session ID
	 * @param  string $authCode   The authorization code
	 * @param  int    $expireTime Unix timestamp of the access token expiry time
	 * @return int                The auth code ID
	 */
	public function associateAuthCode($sessionId, $authCode, $expireTime)
	{
		$data = array(
			'session_id' => $sessionId,
			'auth_code' => $authCode,
			'auth_code_expires' => $expireTime
		);

		$this->insert('oauth_session_authcodes', $data);
	}

	/**
	 * Remove an associated authorization token from a session
	 *
	 * @param  int    $sessionId   The session ID
	 * @return void
	 */
	public function removeAuthCode($sessionId)
	{
		$query = Peyote::delete('oauth_session_authcodes')
			->where('session_id', '=', $sessionId);

		$this->runQuery($query);
	}

	/**
	 * Validate an authorization code
	 *
	 * @param  string     $clientId    The client ID
	 * @param  string     $redirectUri The redirect URI
	 * @param  string     $authCode    The authorization code
	 * @return array|bool              False if invalid or array as above
	 */
	public function validateAuthCode($clientId, $redirectUri, $authCode)
	{
		$query = Peyote::select('oauth_sessions')
			->columnsArray(array(
				'oauth_sessions.id AS session_id',
				'oauth_session_authcodes.id AS authcode_id'
			))->join('oauth_session_authcodes')->on('oauth_session_authcodes.session_id', '=', 'oauth_sessions.id')
			->join('oauth_session_redirects')->on('oauth_sessions_redirects.session_id', '=', 'oauth_sessions.id')
			->where('oauth_sessions.client_id', '=', $clientId)
			->where('oauth_session_authcodes.auth_code', '=', $authCode)
			->where('oauth_session_authcodes.auth_code_expires', '>=', time())
			->where('oauth_session_redirects.redirect_uri', '=', $redirectUri);

		$result = $this->runQuery($query);
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row)
		{
			return false;
		}

		return array(
			'session_id' => (int) $row['session_id'],
			'authcode_id' => (int) $row['authcode_id']
		);
	}

	/**
	 * Validate an access token
	 *
	 * @param  string     $accessToken The access token
	 * @return array|bool              False if invalid or an array as above
	 */
	public function validateAccessToken($accessToken)
	{
		$query = Peyote::select('oauth_session_access_tokens')
			->columnsArray(array(
				'session_id',
				'oauth_sessions.client_id',
				'oauth_sessions.owner_id',
				'oauth_sessions.owner_type'
			))->join('oauth_sessions')->on('oauth_sessions.id', '=', 'session_id')
			->where('access_token', '=', $accessToken)
			->where('access_token_expires', '>=', time());

		$result = $this->runQuery($query);
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row)
		{
			return false;
		}

		$row['session_id'] = (int) $row['session_id'];
		return $row;	
	}

	/**
	 * Removes a refresh token
	 *
	 * @param  string $refreshToken The refresh token to be removed
	 * @return void
	 */
	public function removeRefreshToken($refreshToken)
	{
		$query = Peyote::delete('oauth_session_refresh_tokens')
			->where('refresh_token', '=', $refreshToken);

		$this->runQuery($query);
	}

	/**
	 * Validate a refresh token
	 *
	 * @param  string   $refreshToken The access token
	 * @param  string   $clientId     The client ID
	 * @return int|bool               The ID of the access token the refresh token is linked to (or false if invalid)
	 */
	public function validateRefreshToken($refreshToken, $clientId)
	{
		$query = Peyote::select('oauth_session_refresh_tokens')
			->columns('session_access_token_id')
			->where('refresh_token', '=', $refreshToken)
			->where('refresh_token_expires', '>=', time())
			->where('client_id', '=', $clientId);

		$result = $this->runQuery($query);
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row)
		{
			return false;
		}

		return (int) $row['session_access_token_id'];
	}

	/**
	 * Get an access token by ID
	 *
	 * @param  int    $accessTokenId The access token ID
	 * @return array
	 */
	public function getAccessToken($accessTokenId)
	{
		$query = Peyote::select('oauth_session_access_tokens')
			->where('id', '=', $accessTokenId)
			->limit(1);

		$result = $this->runQuery($query);
		$row = $result->fetch(\PDO::FETCH_ASSOC);

		if ( ! $row)
		{
			return array();
		}

		return array(
			'id' => (int) $row['id'],
			'session_id' => (int) $row['session_id'],
			'access_token' => $row['access_token'],
			'access_token_expires' => (int) $row['access_token_expires']
		);
	}

	/**
	 * Associate scopes with an auth code (bound to the session)
	 *
	 * @param  int $authCodeId The auth code ID
	 * @param  int $scopeId    The scope ID
	 * @return void
	 */
	public function associateAuthCodeScope($authCodeId, $scopeId)
	{
		$data = array(
			'oauth_session_authcode_id' => $authCodeId,
			'scope_id' => $scopeId
		);

		$this->insert('oauth_session_authcode_scopes', $data);
	}

	/**
	 * Get the scopes associated with an auth code
	 *
	 * @param  int   $oauthSessionAuthCodeId The session ID
	 * @return array
	 */
	public function getAuthCodeScopes($oauthSessionAuthCodeId)
	{
		$query = Peyote::select('oauth_session_authcode_scopes')
			->columns('scope_id')
			->where('oauth_session_authcodes_id', '=', $oauthSessionAuthCodeId);

		$statement = $this->runQuery($query);
		$result = $statement->fetchAll(\PDO::FETCH_ASSOC);

		if (count($result) === 0)
		{
			return array();
		}

		return array_map(function($row){
			return array(
				'scope_id' => (int) $row['scope_id']
			);
		}, $result);
	}

	/**
	 * Associate a scope with an access token
	 *
	 * @param  int    $accessTokenId The ID of the access token
	 * @param  int    $scopeId       The ID of the scope
	 * @return void
	 */
	public function associateScope($accessTokenId, $scopeId)
	{
		$data = array(
			'session_access_token_id' => $accessTokenId,
			'scope_id' => $scopeId
		);

		$this->insert('oauth_session_token_scopes', $data);
	}

	/**
	 * Get all associated access tokens for an access token
	 *
	 * @param  string $accessToken The access token
	 * @return array
	 */
	public function getScopes($accessToken)
	{
		$query = Peyote::select('oauth_session_token_scopes')
			->columns('oauth_scopes.*')
			->join('oauth_session_access_tokens')
			->on('oauth_session_access_tokens.id', '=', 'oauth_session_token_scopes.session_access_token_id')
			->join('oauth_scopes')
			->on('oauth_scopes.id', '=', 'oauth_session_token_scopes.scope_id')
			->where('access_token', '=', $accessToken);

		$statement = $this->runQuery($query);
		return $statement->fetchAll(\PDO::FETCH_ASSOC);
	}

}
