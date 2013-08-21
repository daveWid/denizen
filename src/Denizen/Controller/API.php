<?php

namespace Denizen\Controller;

class API extends \Denizen\Controller
{
	/**
	 * Makes sure that there is a valid access token.
	 *
	 * If a valid token is not found then the application halts execution
	 * and throws an error
	 */
	protected function checkToken()
	{
		try
		{
			$this->app->getResourceServer()->isValid(true);
		}
		catch (\League\OAuth2\Server\Exception\InvalidAccessTokenException $e)
		{
			$this->sendJSON(array(
				'error' => $e->getMessage()
			), 403);
		}
	}

	/**
	 * Setup a json response.
	 *
	 * @param array $data    The json data array to send
	 * @param int   $status  The status code
	 */
	protected function sendJSON(array $data, $status = 200)
	{
		$response = $this->app->response();
		$response['Content-Type'] = "application/json;charset=utf-8";
		$response->setStatus($status);

		if ( ! empty($data))
		{
			$response->body(json_encode($data));
		}

		$this->app->stop();
	}

	/**
	 * Adds in do not cache headers to the response
	 */
	protected function noCache()
	{
		$response = $this->app->response();
		$response['Cache-Control'] = 'no-store';
		$response['Pragma'] = 'no-cache';
	}

	/**
	 * Runs validation on the model.
	 * If any errors are present, then the error response is sent immediately.
	 *
	 * @param  \Peyote\Model $model  The model to validate
	 */
	protected function validate($model)
	{
		$errors = $model->validate();
		if ( ! empty($errors))
		{
			$this->sendJSON(array(
				'errors' => $errors
			), 400);
		}
	}

}
