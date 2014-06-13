<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 1.0.b
 * @copyright 2014 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 */
namespace Drone\Core;

use Drone\Core;
use Drone\Core\Logger;

/**
 * Drone View class
 *
 * @author Shay Anderson 05.14 <http://www.shayanderson.com/contact>
 */
class View
{
	/**
	 * Route params
	 *
	 * @var array
	 */
	private $__route_params = [];

	/**
	 * Template path
	 *
	 * @var string
	 */
	private $__template;

	/**
	 * Default template path
	 *
	 * @var string
	 */
	private $__template_default;

	/**
	 * View property setter
	 *
	 * @param string $name
	 * @param mixed $value
	 * @return void
	 */
	public function __set($name, $value)
	{
		if($name === 'view' || $name === 'drone')
		{
			return; // do not allow view|drone props because of extraction for vars
		}

		$this->{$name} = $value;

		drone()->log->trace('Set view param: \'' . $name . '\'', Logger::CATEGORY_DRONE);
	}

	/**
	 * Format template path
	 *
	 * @param string $template
	 * @return string
	 */
	private static function __formatTemplate($template)
	{
		if(strlen($template) < 1)
		{
			$template = 'index';
		}

		if(substr($template, -(strlen(drone()->get(Core::KEY_EXT_TEMPLATE))))
			!== drone()->get(Core::KEY_EXT_TEMPLATE))
		{
			$template .= drone()->get(Core::KEY_EXT_TEMPLATE);
		}

		return $template;
	}

	/**
	 * Clear all view properties
	 *
	 * @return void
	 */
	public function clearProperties()
	{
		foreach($this->getProperties() as $k => $v)
		{
			unset($this->{$k});
		}
	}

	/**
	 * Display view template
	 *
	 * @param string $template
	 * @return void
	 */
	public function display($template = null)
	{
		$this->__template = !is_null($template)
			? drone()->get(Core::KEY_PATH_TEMPLATE) . self::__formatTemplate($template)
			: self::__formatTemplate($this->__template_default);
	}

	/**
	 * View properties getter
	 *
	 * @return array (ex: ['prop1' => x, 'prop2' => y, ...])
	 */
	public function getProperties()
	{
		$props = get_object_vars($this);
		unset($props['__template'], $props['__template_default']);
		return $props;
	}

	/**
	 * Template path getter
	 *
	 * @return string
	 */
	public function getTemplate()
	{
		return $this->__template;
	}

	/**
	 * Route param getter
	 *
	 * @param string $key
	 * @param mixed $array_key (string when getting array key value)
	 * @return mixed (false on param does not exist)
	 */
	public function param($key, $array_key = null)
	{
		if(is_null($key)) // get all
		{
			return $this->__route_params;
		}

		if(is_null($array_key))
		{
			if(isset($this->__route_params[$key]) || array_key_exists($key, $this->__route_params))
			{
				return $this->__route_params[$key];
			}
		}
		else // array key
		{
			if(isset($this->__route_params[$key]) && is_array($this->__route_params[$key]))
			{
				if(isset($this->__route_params[$key][$array_key])
					|| array_key_exists($array_key, $this->__route_params[$key]))
				{
					return $this->__route_params[$key][$array_key];
				}
			}
		}

		return false;
	}

	/**
	 * Reset template
	 *
	 * @return void
	 */
	public function resetTemplate()
	{
		$this->__template = null;
	}

	/**
	 * Default template setter (called from \Drone\Core)
	 *
	 * @param string $template
	 * @return void
	 */
	public function setDefaultTemplate($template)
	{
		$this->__template_default = $template;
	}

	/**
	 * Route params setter (called from \Drone\Core)
	 *
	 * @param array $params
	 * @return void
	 */
	public function setRouteParams(array &$params)
	{
		if(count($this->__route_params) < 1) // only init once
		{
			$this->__route_params = &$params;
		}
	}

	/**
	 * Template to formatted path getter
	 *
	 * @param string $template (ex: 'my_template')
	 * @return string (ex: '/var/www/proj/.../my_template.tpl')
	 */
	public function template($template)
	{
		$template = drone()->get(Core::KEY_PATH_TEMPLATE) . self::__formatTemplate($template);

		if($template === $this->__template) // duplicate view template, stop template loop + memory overload
		{
			drone()->error(Core::ERROR_500, 'View template loop detected');
			return '';
		}

		return $template;
	}

	/**
	 * Template to formatted path getter using global template path
	 *
	 * @param string $template (ex: 'my_template')
	 * @return string (ex: '/var/www/proj/.../_global/my_template.tpl')
	 */
	public function templateGlobal($template)
	{
		return drone()->get(Core::KEY_PATH_TEMPLATE_GLOBAL) . self::__formatTemplate($template);
	}
}