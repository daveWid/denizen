<?php

namespace Denizen;

class Controller
{
	/**
	 * @var \Denizen\Application  The application instance
	 */
	protected $app;

	/**
	 * @param \Denizen\Application $app  The application instance
	 */
	public function __construct($app)
	{
		$this->app = $app;
		$this->init();
	}

	/**
	 * Initialization function to avoid the dependency injection in the constructor
	 */
	public function init(){}

	/**
	 * Execute a method of the controller, calling before and after.
	 *
	 * @param  string $action  Name of the action to run
	 * @return \Denizen\Controller
	 */
	public function execute($action)
	{
		$args = \func_get_args();
		\array_shift($args);

		$this->app->applyHook('denizen.preDispatch');
		\call_user_func_array(array($this, $action), $args);
		$this->app->applyHook('denizen.postDispatch');

		return $this;
	}

}
