<?php
/**
 * Drone - Rapid Development Framework for PHP 5.5.0+
 *
 * @package Drone
 * @version 0.2.3
 * @copyright 2015 Shay Anderson <http://www.shayanderson.com>
 * @license MIT License <http://www.opensource.org/licenses/mit-license.php>
 * @link <https://github.com/shayanderson/drone>
 */

/**
 * Application + Drone Bootstrap
 */

//////////////////////////////////////////////////////////////////////////
// Logging
//////////////////////////////////////////////////////////////////////////
// set logging level
drone()->log->setLogLevel(\Drone\Logger::LEVEL_TRACE);
// drone()->log->setLogFile('_app/var/drone.log'); // set log file (optional)


//////////////////////////////////////////////////////////////////////////
// Settings
//////////////////////////////////////////////////////////////////////////
// framework settings
// \Drone\Registry::set(\Drone\Core::KEY_DEBUG, false); // debug mode - off for production
// \Drone\Registry::set(\Drone\Core::KEY_ERROR_BACKTRACE, false); // backtrace in log - off for production
// \Drone\Registry::set(\Drone\Core::KEY_ERROR_LOG, true); // errors to server log - on for production


//////////////////////////////////////////////////////////////////////////
// Error handlers
//////////////////////////////////////////////////////////////////////////
drone()->error(function($error) { pa('<div style="color:#f00;">' . $error,
	debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS), '</div>'); });
drone()->error(404, function() { drone()->run('error->\ErrorController->_404'); });
drone()->error(500, function() { drone()->run('error->\ErrorController->_500'); });


//////////////////////////////////////////////////////////////////////////
// Hooks
//////////////////////////////////////////////////////////////////////////
// after hook that displays log example:
drone()->hook(\Drone\Core::HOOK_AFTER, function() { pa('', 'Log:', drone()->log->get()); });


//////////////////////////////////////////////////////////////////////////
// Mapped routes
//////////////////////////////////////////////////////////////////////////
// example: drone()->route(['/user/:id' => 'user->view']);