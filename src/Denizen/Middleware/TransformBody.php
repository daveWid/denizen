<?php

namespace Denizen\Middleware;

/**
 * Looks for an application/json media type and transforms it into the form
 * hash so it cal be used internally by the Slim\Request class.
 */
class TransformBody extends \Slim\Middleware
{
	public function call()
	{
		if ($this->app->request()->getMediaType() === 'application/json')
		{
			$result = json_decode($this->app->request->getBody(), true);
			if ($result)
			{
				$this->app->environment['slim.request.form_hash'] = $result;
			}
		}

		$this->next->call();
	}
}
