<?php

namespace Denizen\Controller;

class OAuth2 extends API
{
	public function token()
	{
		$type = $this->app->request()->post('grant_type', 'password');

		$grant = $this->app->getAuthServer()->getGrantType($type);

		if ($type === 'password')
		{
			$grant->setVerifyCredentialsCallback(array($this, 'verify'));
		}

		$this->noCache();

		try
		{
			$token = $grant->completeFlow();
			$this->sendJSON($token);
		}
		catch (\League\OAuth2\Server\Exception\ClientException $e)
		{
			$this->sendJSON(array(
				'error' => \League\OAuth2\Server\Authorization::getExceptionType($e->getCode()),
				'error_description' => $e->getMessage()
			), 400);

			$this->app->stop();
		}
	}

	public function verify($user, $password)
	{
		$user = $this->app->table('\\Denizen\\Table\\Users')->login($user, $password);

		if (empty($user) === true)
		{
			return false;
		}

		return $user['id'];
	}
}
