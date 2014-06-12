<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.b - Jun 12, 2014
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone;

use Drone\Core\Logger;
use Drone\Core\Route;

/**
 * Drone Core class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 *
 * @property \Drone\Core\Data $data
 * @property \Drone\Core\Flash $flash
 * @property \Drone\Core\Logger $log
 * @property \Drone\Core\Request $request
 * @property \Drone\Core\Session $session
 * @property \Drone\Core\View $view
 */
class Core
{
	/**
	 * Application error codes
	 */
	const
		ERROR_403 = 403,
		ERROR_404 = 404,
		ERROR_500 = 500;

	/**
	 * Hook types
	 */
	const
		HOOK_AFTER = 1,
		HOOK_BEFORE = 2;

	/**
	 * Framework param keys
	 */
	const
		KEY_DEBUG = '__DRONE__.debug', // debug mode
		KEY_ERROR_BACKTRACE = '__DRONE__.error.backtrace', // backtrace to log on error
		KEY_ERROR_HANDLER = '__DRONE__.error.handler', // callable error handler
		KEY_ERROR_LOG = '__DRONE__.error.log',  // log error in web server error log
		KEY_EXT_TEMPLATE = '__DRONE__.ext.template', // template file extension
		KEY_EXT_WEB = '__DRONE__.ext.web', // web/public page extension
		KEY_PATH_CONTROLLER = '__DRONE__.path.controller', // controller load path
		KEY_PATH_TEMPLATE = '__DRONE__.path.template', // template load path
		KEY_PATH_TEMPLATE_GLOBAL = '__DRONE__.path.template_global', // global template load path
		KEY_ROUTE_ACTION = '__DRONE__.route.action', // route action
		KEY_ROUTE_CONTROLLER = '__DRONE__.route.controller', // route controller
		KEY_ROUTE_TEMPLATE = '__DRONE__.route.template'; // route template

	/**
	 * Package version
	 */
	const VERSION = '1.0.b';

	/**
	 * Last error message
	 *
	 * @var string
	 */
	private static $__error_last;

	/**
	 * Events
	 *
	 * @var array
	 */
	private $__events = [];

	/**
	 * Hooks
	 *
	 * @var array
	 */
	private $__hooks = [];

	/**
	 * Core objects (lazy loaded)
	 *
	 * @var array
	 */
	private $__objects = [];

	/**
	 * Core objects map
	 *
	 * @var array
	 */
	private static $__objects_map = [
		'data' => '\Drone\Core\Data',
		'flash' => '\Drone\Core\Flash',
		'log' => '\Drone\Core\Logger',
		'request' => '\Drone\Core\Request',
		'session' => '\Drone\Core\Session',
		'view' => '\Drone\Core\View'
	];

	/**
	 * Framework params
	 *
	 * @var array
	 */
	private $__params = [];

	/**
	 * Mapped routes
	 *
	 * @var array (\Drone\Core\Route)
	 */
	private $__routes = [];

	/**
	 * Restricted init
	 */
	private function __construct()
	{
		$this->timer(); // init core timer
	}

	/**
	 * Restrict
	 */
	private function __clone()
	{
		// nothing
	}

	/**
	 * Core object lazy loader and property getter
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function &__get($name)
	{
		if(isset(self::$__objects_map[$name]))
		{
			if(!isset($this->__objects[$name]))
			{
				$this->__objects[$name] = new self::$__objects_map[$name];

				if(isset($this->__objects['log']))
				{
					$this->__objects['log']->trace('Loaded core object: \'' . $name . '\'',
						Logger::CATEGORY_DRONE);
				}
			}

			return $this->__objects[$name];
		}

		return $this->{$name};
	}

	/**
	 * Core property setter with core object protection
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		if(!isset(self::$__objects_map[$name])) // protect core objects
		{
			$this->{$name} = $value;
		}
	}

	/**
	 * Restrict
	 */
	private function __wakeup()
	{
		// nothing
	}

	/**
	 * Clean buffer if needed
	 *
	 * @return void
	 */
	private function __bufferClean()
	{
		// check buffer
		if(ob_get_level() !== 0)
		{
			ob_clean(); // clean buffer
		}
	}

	/**
	 * Format directory path, ex: '/my/path' => '/my/path/'
	 *
	 * @param string $dir
	 * @return void
	 */
	private function __formatDir(&$dir)
	{
		$dir = rtrim($dir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
	}

	/**
	 * Send headers
	 *
	 * @return void
	 */
	private function __headersSend()
	{
		if(!headers_sent()) // send headers if not sent already
		{
			foreach($this->header(null) as $k => $v)
			{
				if(is_int($k)) // string header, ex: 'HTTP/1.0 404 Not Found'
				{
					header($v);
				}
				else // name/value pair, ex: 'Location' => 'http://...'
				{
					header($k . ': ' . $v, false);
				}
			}
		}
	}

	/**
	 * Clear/unset param key/value pair
	 *
	 * @param string $key
	 * @return void
	 */
	public function clear($key)
	{
		if($this->has($key))
		{
			unset($this->__params[$key]);
			$this->log->trace('Cleared param: \'' . $key . '\'', Logger::CATEGORY_DRONE);
		}
	}

	/**
	 * Trigger error or register error handler for error code
	 *
	 * @example
	 *		->error('My error'); // custom error to default error (500 error)
	 *		->error(404); // issue 404
	 *		->error(404, 'My message'); // issue 404 with message
	 *		->error(404, function() {}); // register error handler for 404 code
	 *		->error(function() {}); // register default error handler
	 *		->error($exception); // handle \Exception
	 *
	 * @staticvar array $handlers
	 * @staticvar boolean $is_error
	 * @param mixed $code
	 * @param mixed $message (optional)
	 * @return mixed
	 */
	public function error($code = null, $message = null)
	{
		static $handlers = [];
		static $is_error = false;

		if(is_null($code)) // get error occurred flag
		{
			return $is_error;
		}
		if($code === false) // reset flag
		{
			$is_error = false;
			return;
		}
		else if(is_callable($code)) // register default error handler
		{
			self::errorHandler($code, null, null, null);
			return;
		}
		else if(is_callable($message)) // register error handler
		{
			$handlers[$code] = $message;
			$this->log->trace('Error \'' . $code . '\' handler registered', Logger::CATEGORY_DRONE);
			return; // registered
		}
		else if($code instanceof \Exception) // handle exception
		{
			$this->error($code->getCode(), $code->getMessage());
			return; // handled
		}

		$this->__bufferClean(); // clean buffer before error buffer

		$is_error = true; // flag error

		if(is_null($message)) // auto set message
		{
			switch($code)
			{
				case self::ERROR_403:
					$message = 'Forbidden';
					break;

				case self::ERROR_404:
					$message = 'Not Found';
					break;

				case self::ERROR_500:
					$message = 'Internal Server Error';
					break;
			}
		}

		if(is_null($message)) // auto set message fallback
		{
			if(!isset($handlers[$code]) && is_string($code))
			{
				$message = $code;
			}
			else
			{
				$message = 'An unknown error has occurred';
			}
		}

		self::$__error_last = $message; // cache last error

		if($code == self::ERROR_404) // log error
		{
			$this->log->error($message);
			$this->header(self::ERROR_404); // issue 404 header
		}
		else // fatal log
		{
			if($code == self::ERROR_403)
			{
				$this->header(self::ERROR_403); // issue 403 header
			}
			$this->log->fatal($message);
		}

		// add backtrace to log (not on 404)
		if($code != self::ERROR_404 && $this->get(self::KEY_ERROR_BACKTRACE))
		{
			$this->log->trace('Debug backtrace: '
				. print_r(debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), true), Logger::CATEGORY_DRONE);
		}

		// call error handler
		if(isset($handlers[$code])) // call custom error handler
		{
			$handlers[$code]();
		}
		else if(isset($handlers[self::ERROR_500])) // no custom error handler, call 500 error
		{
			$handlers[self::ERROR_500]();
		}
		else // no handlers, issue 500 header (fallback)
		{
			$this->header(self::ERROR_500);
			$this->__headersSend(); // send headers
		}

		$this->stop(); // stop application
	}

	/**
	 * Global error handler
	 *
	 * @param int $err_no
	 * @param string $err_message
	 * @param string $err_file
	 * @param int $err_line
	 * @return boolean
	 */
	public static function errorHandler($err_no, $err_message, $err_file, $err_line)
	{
		static $handler;

		if(is_callable($err_no)) // register default error handler
		{
			$handler = $err_no;
			drone()->log->trace('Error default handler registered', Logger::CATEGORY_DRONE);
			return true;
		}

		if(!($err_no & error_reporting()))
		{
			return false;
		}

		$err_message .= ' (' . $err_file . ':' . $err_line . ')';

		switch($err_no)
		{
			case \E_ERROR:
			case \E_USER_ERROR:
				$err_message = 'Error: ' . $err_message;
				drone()->log->fatal($err_message, Logger::CATEGORY_DRONE);
				break;

			case \E_WARNING:
			case \E_USER_WARNING:
				$err_message = 'Warning: ' . $err_message;
				drone()->log->warn($err_message, Logger::CATEGORY_DRONE);
				break;

			case \E_NOTICE:
			case \E_USER_NOTICE:
				$err_message = 'Notice: ' . $err_message;
				drone()->log->debug($err_message, Logger::CATEGORY_DRONE);
				break;
		}

		if(drone()->get(self::KEY_ERROR_LOG)) // log error
		{
			error_log($err_message, $err_no);
		}

		self::$__error_last = $err_message; // cache error message

		if(is_callable($handler)) // call default error handler
		{
			$handler($err_message);
		}

		if($err_no === \E_ERROR || $err_no === \E_USER_ERROR) // fatal
		{
			drone()->stop();
		}

		return true; // error handled
	}

	/**
	 * Last error message getter
	 *
	 * @return string (or null on no last error message)
	 */
	public function errorLast()
	{
		return self::$__error_last;
	}

	/**
	 * Register application event
	 *
	 * @param string $key (or array for multiple load)
	 * @param callable $event
	 * @return void
	 */
	public function event($key, callable $callable)
	{
		if(is_array($key)) // multiple load
		{
			foreach($key as $k => $v)
			{
				$this->event($k, $v);
			}
			return;
		}

		if(!isset($this->__events[$key]))
		{
			$this->log->trace('Event registered: \'' . $key . '\'', Logger::CATEGORY_DRONE);
			$this->__events[$key] = $callable;
		}
	}

	/**
	 * Param value getter
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get($key)
	{
		return $this->has($key) ? $this->__params[$key] : null;
	}

	/**
	 * Param array getter
	 *
	 * @return array
	 */
	public function getAll()
	{
		return $this->__params;
	}

	/**
	 * Self instance getter
	 *
	 * @staticvar self $self
	 * @return \self
	 */
	public static function &getInstance()
	{
		static $self;

		if(is_null($self)) // init
		{
			$self = new self;
		}

		return $self;
	}

	/**
	 * Param exists flag getter
	 *
	 * @param string $key
	 * @return boolean
	 */
	public function has($key)
	{
		return isset($this->__params[$key]) || array_key_exists($key, $this->__params);
	}

	/**
	 * Add header name/value
	 *
	 * @param string|int $name
	 * @param mixed $value
	 * @return array
	 */
	public function header($name, $value = null)
	{
		static $headers = [];

		if(!is_null($name)) // setter
		{
			if(is_null($value)) // use predefined header value
			{
				switch($name)
				{
					case self::ERROR_403:
						$headers[] = 'HTTP/1.0 403 Forbidden';
						break;

					case self::ERROR_404:
						$headers[] = 'HTTP/1.0 404 Not Found';
						break;

					case self::ERROR_500:
						$headers[] = 'HTTP/1.0 500 Internal Server Error';
						break;
				}
			}
			else
			{
				$headers[$name] = $value;
			}
		}

		return $headers;
	}

	/**
	 * Register hook
	 *
	 * @param int $type
	 * @param callable $hook
	 * @return void
	 */
	public function hook($type, callable $hook)
	{
		$this->__hooks[$type][] = $hook;
		$this->log->trace('Hook registered (' . ( $type == self::HOOK_BEFORE ? 'before' : 'after' ) . ')',
			Logger::CATEGORY_DRONE);
	}

	/**
	 * Force redirect to URL
	 *
	 * @param string $location
	 * @param boolean $use_301 (permanent 301 flag)
	 * @return void
	 */
	public function redirect($location, $use_301 = false)
	{
		if(!headers_sent())
		{
			header('Location: ' . $location, true, $use_301 ? 301 : null);
			$this->stop();
		}
	}

	/**
	 * Add dynamic route
	 *
	 * @param string $path (or array for multiple load, ex: '/my/route/:param')
	 * @param string $controller (ex: 'my/route', or with action: 'my/route->action')
	 * @return void
	 */
	public function route($path, $controller = null)
	{
		if(is_array($path))
		{
			foreach($path as $k => $v)
			{
				$this->route($k, $v);
			}
			return;
		}

		$this->__routes[] = new Route($path, $controller);
		$this->log->trace('Route registered: \'' . $path . '\'', Logger::CATEGORY_DRONE);
	}

	/**
	 * Run application
	 *
	 * @staticvar boolean $is_init
	 * @staticvar array $routes
	 * @param $route (optional, ex: 'my/route->action')
	 * @return void
	 */
	public function run($route = null)
	{
		static $is_init = false;
		static $routes = []; // routes stack for current request

		if(!$is_init) // init
		{
			$this->log->trace('Initializing', Logger::CATEGORY_DRONE);

			// param default values
			$default = [
				self::KEY_DEBUG => true,
				self::KEY_ERROR_BACKTRACE => true,
				self::KEY_ERROR_HANDLER => ['\Drone\Core', 'errorHandler'],
				self::KEY_ERROR_LOG => false,
				self::KEY_EXT_TEMPLATE => '.tpl',
				self::KEY_EXT_WEB => '.htm',
				self::KEY_PATH_CONTROLLER => PATH_ROOT . '_app/mod',
				self::KEY_PATH_TEMPLATE => PATH_ROOT . '_app/tpl',
				self::KEY_PATH_TEMPLATE_GLOBAL => PATH_ROOT . '_app/tpl/_global'
			];

			// init param default values
			foreach($default as $k => $v)
			{
				if(!$this->has($k))
				{
					$this->set($k, $v);
				}
			}

			// set default error handler
			if(is_array($this->get(self::KEY_ERROR_HANDLER)))
			{
				set_error_handler($this->get(self::KEY_ERROR_HANDLER));
			}

			// init paths
			$this->__formatDir($this->__params[self::KEY_PATH_CONTROLLER]);
			$this->__formatDir($this->__params[self::KEY_PATH_TEMPLATE]);
			$this->__formatDir($this->__params[self::KEY_PATH_TEMPLATE_GLOBAL]);

			$is_init = true;
		}

		$this->set(self::KEY_ROUTE_CONTROLLER, false); // init controller

		if(!is_null($route)) // fire manual route
		{
			$routes[] = $route; // cache route
			$route = new Route(null, $route);
			$this->set([
				self::KEY_ROUTE_CONTROLLER => $route->getController(),
				self::KEY_ROUTE_TEMPLATE => $route->getController()
			]);

			if($route->isAction())
			{
				$this->set(self::KEY_ROUTE_ACTION, $route->getAction());
			}

			$this->log->trace('Route set: \'' . $this->get(self::KEY_ROUTE_CONTROLLER) . '\'',
				Logger::CATEGORY_DRONE);
		}
		else // detect route
		{
			$is_index = false;
			$request = $_SERVER['REQUEST_URI'];
			if(($pos = strpos($request, '?')) !== false) // rm query string
			{
				$request = substr($request, 0, $pos);
			}
			unset($pos);

			$routes[] = $request;

			if(substr($request, -1) != '/') // request is like '/page.htm'
			{
				// ensure request has web extension
				if(substr($request, -(strlen($this->get(self::KEY_EXT_WEB)))) === $this->get(self::KEY_EXT_WEB))
				{
					// do not allow direct access to index like '/path/index.htm'
					if(basename($request) === 'index' . $this->get(self::KEY_EXT_WEB))
					{
						$this->error(self::ERROR_404); // kick direct index request
						return;
					}

					// rm web extension
					$request = substr($request, 0, strlen($request) - strlen($this->get(self::KEY_EXT_WEB)));
				}
				else // no web extension (not allowed)
				{
					$this->error(self::ERROR_404); // kick request
					return;
				}
			}
			else // add default (index) controller flag
			{
				$is_index = true;
			}

			// test mapped routes
			if($request !== '/') // if not base '/'
			{
				foreach($this->__routes as $r)
				{
					if($r->match($request))
					{
						$this->set([
							self::KEY_ROUTE_CONTROLLER => $r->getController(),
							self::KEY_ROUTE_TEMPLATE => $r->getController()
						]);

						if($r->isAction())
						{
							$this->set(self::KEY_ROUTE_ACTION, $r->getAction());
						}
						
						$this->view->setRouteParams($r->getParams()); // set route params

						$this->log->trace('Route (mapped) detected: \''
							. $this->get(self::KEY_ROUTE_CONTROLLER) . '\'', Logger::CATEGORY_DRONE);
						break;
					}
				}

				unset($r);
			}

			// test static routes
			if($this->get(self::KEY_ROUTE_CONTROLLER) === false)
			{
				$request = str_replace('/', DIRECTORY_SEPARATOR, $request);

				if($is_index) // add default controller
				{
					$request .= 'index';
				}

				$this->set([
					self::KEY_ROUTE_CONTROLLER => $request,
					self::KEY_ROUTE_TEMPLATE => $request
				]);
				$this->log->trace('Route (static) detected: \'' . $this->get(self::KEY_ROUTE_CONTROLLER)
					. '\'', Logger::CATEGORY_DRONE);

			}

			// cleanup
			unset($is_index, $request);
		}

		if(max(array_count_values($routes)) > 1) // duplicate routes, stop route loop + memory overload
		{
			$routes = []; // reset
			$this->error(self::ERROR_500, 'Route loop detected');
			return;
		}

		// set full paths + extensions
		$this->set(self::KEY_ROUTE_CONTROLLER, $this->get(self::KEY_PATH_CONTROLLER)
			. ltrim($this->get(self::KEY_ROUTE_CONTROLLER), DIRECTORY_SEPARATOR) . '.php');
		$this->set(self::KEY_ROUTE_TEMPLATE, $this->get(self::KEY_PATH_TEMPLATE)
			. ltrim($this->get(self::KEY_ROUTE_TEMPLATE), DIRECTORY_SEPARATOR)
			. $this->get(self::KEY_EXT_TEMPLATE));

		try // run controller
		{
			$this->view->resetTemplate(); // reset template (for multiple runs like errors)
			$this->view->setDefaultTemplate($this->get(self::KEY_ROUTE_TEMPLATE)); // set default template

			$this->error(false); // reset error flag

			if(is_file($this->get(self::KEY_ROUTE_CONTROLLER)))
			{
				ob_start(); // buffer output

				if(isset($this->__hooks[self::HOOK_BEFORE])) // fire before hooks
				{
					$this->log->trace('Calling before hook(s)', Logger::CATEGORY_DRONE);
					foreach($this->__hooks[self::HOOK_BEFORE] as $hook)
					{
						$hook();
					}
					unset($hook);
				}

				$this->log->trace('Loading controller: \'' . $this->get(self::KEY_ROUTE_CONTROLLER) . '\'',
					Logger::CATEGORY_DRONE);

				require_once $this->get(self::KEY_ROUTE_CONTROLLER);

				$this->__headersSend(); // send headers

				// call controller action
				if($this->has(self::KEY_ROUTE_ACTION))
				{
					$this->log->trace('Calling action: \'' . $this->get(self::KEY_ROUTE_ACTION) . '\'',
						Logger::CATEGORY_DRONE);

					if(!class_exists('\Controller', false))
					{
						$this->error(self::ERROR_500, 'Class \'Controller\' not found when calling route action');
						return;
					}

					if(!method_exists('\Controller', $this->get(self::KEY_ROUTE_ACTION)))
					{
						$this->error(self::ERROR_500, 'Method \'Controller::' . $this->get(self::KEY_ROUTE_ACTION)
							. '\' not found when calling route action');
						return;
					}

					// set controller instance
					$controller = new \Controller;

					if(method_exists($controller, '__before'))
					{
						$controller->__before();
					}

					// call controller action
					$controller->{$this->get(self::KEY_ROUTE_ACTION)}();

					if(method_exists($controller, '__after'))
					{
						$controller->__after();
					}
				}
				// check for deny direct access to controller without action
				else if(class_exists('\Controller', false) && defined('\Controller::DENY'))
				{
					$this->error(self::ERROR_404, 'Deny no action');
					return;
				}

				if(!$this->get(self::KEY_DEBUG)) // only flush if not debugging
				{
					$this->__bufferClean();
				}

				// view display template
				if(!is_null($this->view->getTemplate()))
				{
					$this->log->trace('Loading view template: \'' . $this->view->getTemplate() . '\'',
						Logger::CATEGORY_DRONE);

					if(!is_file($this->view->getTemplate()))
					{
						$this->error(self::ERROR_500, 'View template \'' . $this->view->getTemplate()
							. '\' not found');
						return;
					}

					// extract all view public properties for variable use in template
					extract(get_object_vars($this->view), EXTR_OVERWRITE);

					require $this->view->getTemplate();
				}

				if(!$this->error()) // no error, output
				{
					ob_end_flush(); // flush buffer
					$this->stop(); // finalize application
				}
				else // error occurred
				{
					ob_end_clean(); // clean buffer
					$this->__bufferClean();

					$this->error(self::ERROR_500); // call 500 error handler
				}
			}
			else // call 404 error handler
			{
				$this->error(self::ERROR_404);
			}
		}
		catch(\Exception $ex)
		{
			$this->error($ex);
		}
	}

	/**
	 * Param value setter
	 *
	 * @param array|string $key (array ex: ['k1' => 'v1', ...])
	 * @param mixed $value
	 * @return void
	 */
	public function set($key, $value = null)
	{
		if(!is_array($key))
		{
			if(!$this->has($key)) // only log initial time
			{
				$this->log->trace('Set param: \'' . $key . '\'', Logger::CATEGORY_DRONE);
			}
			$this->__params[$key] = $value;
		}
		else
		{
			foreach($key as $k => $v)
			{
				$this->set($k, $v);
			}
		}
	}

	/**
	 * Stop application
	 *
	 * @return void
	 */
	public function stop()
	{
		$this->log->trace('Finalizing', Logger::CATEGORY_DRONE);

		if(isset($this->__hooks[self::HOOK_AFTER]))
		{
			$this->log->trace('Calling after hook(s)', Logger::CATEGORY_DRONE);
			foreach($this->__hooks[self::HOOK_AFTER] as $hook)
			{
				$hook();
			}
		}
		exit;
	}

	/**
	 * Debugging timer method for elapsed time
	 *
	 * @staticvar array $timers
	 * @param string $name
	 * @param int $decimals
	 * @return mixed (float|int)
	 */
	public function timer($name = null, $decimals = 5)
	{
		static $timers = [];

		if(is_null($name))
		{
			$name = '__CORE__';
		}

		if(!isset($timers[$name])) // setter
		{
			$timers[$name] = microtime(true);
			return 0;
		}
		else // getter
		{
			return number_format(microtime(true) - $timers[$name], (int)$decimals, '.', '');
		}
	}

	/**
	 * Trigger event
	 *
	 * @param string $event_key
	 * @param mixed $_ (optional values for callable params)
	 * @return mixed (event callable return type)
	 * @throws \OutOfBoundsException
	 */
	public function trigger($event_key, $_ = null)
	{
		if(!isset($this->__events[$event_key]))
		{
			throw new \OutOfBoundsException('Failed to trigger event \'' . $event_key
				. '\', event key not found');
		}

		$params = func_get_args();
		array_shift($params); // rm event key

		$this->log->trace('Triggering event: \'' . $event_key . '\'', Logger::CATEGORY_DRONE);

		return call_user_func_array($this->__events[$event_key], $params);
	}
}