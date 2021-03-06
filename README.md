# Drone
### Rapid Development Framework for PHP 5.5.0+

Install Drone options:
- Git clone: git clone `https://github.com/shayanderson/drone.git`
- Subversion checkout URL: `https://github.com/shayanderson/drone/trunk`
  - Subversion checkout library files only: `https://github.com/shayanderson/drone/trunk/lib/Drone`
- Download [ZIP file](https://github.com/shayanderson/drone/archive/master.zip)

#### Documentation Topics
- **[Quick Start](https://github.com/shayanderson/drone#quick-start)**: [Directory Structure](https://github.com/shayanderson/drone#directory-structure), [Class Autoloading](https://github.com/shayanderson/drone#class-autoloading), [Drone Function](https://github.com/shayanderson/drone#drone-function), [Helper Functions](https://github.com/shayanderson/drone#helper-functions), [Settings](https://github.com/shayanderson/drone#settings), [Run Application](https://github.com/shayanderson/drone#run-application)
- **[Routes](https://github.com/shayanderson/drone#routes)**: [Static Routes](https://github.com/shayanderson/drone#static-routes), [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes), [Route Files](https://github.com/shayanderson/drone#route-files)
- **[Controllers](https://github.com/shayanderson/drone#controllers)**: [Controller Class](https://github.com/shayanderson/drone#controller-class)
- **[Views](https://github.com/shayanderson/drone#views)**: [View Templates](https://github.com/shayanderson/drone#view-templates)
- **[Logging](https://github.com/shayanderson/drone#logging)**: [Log Levels](https://github.com/shayanderson/drone#log-levels), [Log Configuration](https://github.com/shayanderson/drone#log-configuration), [Custom Log Handler](https://github.com/shayanderson/drone#custom-log-handler)
- **[Error Handling](https://github.com/shayanderson/drone#error-handling)**: [Setting Error Handlers](https://github.com/shayanderson/drone#setting-error-handlers)
- **[Core Methods](https://github.com/shayanderson/drone#core-methods)**: [Registry](https://github.com/shayanderson/drone#registry), [Route Parameters](https://github.com/shayanderson/drone#route-parameters), [Hooks](https://github.com/shayanderson/drone#hooks), [Redirect](https://github.com/shayanderson/drone#redirect), [Headers](https://github.com/shayanderson/drone#headers), [Timers](https://github.com/shayanderson/drone#timers), [Stop Application](https://github.com/shayanderson/drone#stop-application)
- **[Request Variables](https://github.com/shayanderson/drone#request-variables)**
- **[Session Handling](https://github.com/shayanderson/drone#session-handling)**: [Flash Messages](https://github.com/shayanderson/drone#flash-messages)
- **[Data Handling](https://github.com/shayanderson/drone#data-handling)**: [Filter](https://github.com/shayanderson/drone#filter), [Format](https://github.com/shayanderson/drone#format), [Validate](https://github.com/shayanderson/drone#validate)
- **[Database Handling](https://github.com/shayanderson/drone#database-handling)**
- Helper Classes
  - [Config Helper](https://github.com/shayanderson/drone/blob/master/docs/conf.md)
  - **[Filesystem](https://github.com/shayanderson/drone/blob/master/docs/filesystem.md)**: [Directory](https://github.com/shayanderson/drone/blob/master/docs/filesystem.md#dronefilesystemdirectory), [File](https://github.com/shayanderson/drone/blob/master/docs/filesystem.md#dronefilesystemfile)
  - View: [Breadcrumb](https://github.com/shayanderson/drone/blob/master/docs/view-breadcrumb.md), [Decorate](https://github.com/shayanderson/drone/blob/master/docs/view-decorate.md), [Form](https://github.com/shayanderson/drone/blob/master/docs/view-form.md), [Table](https://github.com/shayanderson/drone/blob/master/docs/view-table.md)

## Quick Start
To install Drone simply download the package and install in your project directory. For Apache use the `./.htaccess` file, for Nginx refer to the `./nginx.conf` example configuration file.

#### Directory Structure
By default Drone uses the following directory structure:
- **_app** (framework + application source files)
  - **com** (common application files)
  - **lib** (framework + application class files)
  - **mod** (controller files)
  - **tpl** (view template files)
    - **_global** (global view template files)
- **skin** (front asset files)

The directories for controllers (`_app/mod`), templates (`_app/tpl`) and global templates (`_app/tpl/_global`) can be changed using Drone settings.

#### Class Autoloading
Class autoloading is completed using the `autoload()` function in the `index.php` file, example:
```php
// set class autoloading paths
autoload([
	PATH_ROOT . '_app/lib',
	PATH_ROOT . '_app/mdl'
]);
```
In this example classes will be autoloaded from the `_app/lib` and the `_app/mdl` directories. The autoloader expects the use of namespaces, example:
```php
$myobj = new \Mylib\Myclass;
```
Would load the class `_app/lib/Mylib/Myclass.php` or `_app/mdl/Mylib/Myclass.php` (depending on where the class is located).

#### Drone Function
The `drone()` function can be used to easily access the Drone core class, example:
```php
drone()->header('Content-type', 'image/jpeg');
```

#### Helper Functions
Drone helper functions can be used to access Drone components easily, example of the `request()` helper function:
```php
$name = request()->post('name'); // get POST request value for 'name'
```
Drone helper functions available:
- [`data()`](https://github.com/shayanderson/drone#data-handling) - `drone()->data` alias
- [`error()`](https://github.com/shayanderson/drone#error-handling) - trigger error (`drone()->error()` alias)
- [`error_last()`](https://github.com/shayanderson/drone#error-handling) - get last error (`drone()->errorLast()` alias)
- [`flash()`](https://github.com/shayanderson/drone#flash-messages) - set flash message (`drone()->flash` alias)
- [`logger()`](https://github.com/shayanderson/drone#logging) - `drone()->log` alias
- `pa()` - string/array printer
- [`param()`](https://github.com/shayanderson/drone#route-parameters) - get route param (similar to `view()->param()`)
- [`redirect()`](https://github.com/shayanderson/drone#redirect) - redirect to location (`drone()->redirect()` alias)
- [`request()`](https://github.com/shayanderson/drone#request-variables) - `drone()->request` alias
- [`session()`](https://github.com/shayanderson/drone#session-handling) - `drone()->session` alias
- [`template()`](https://github.com/shayanderson/drone#view-templates) - get template formatted name (`drone()->view->template()` alias)
- [`template_global()`](https://github.com/shayanderson/drone#view-templates) - get global template formatted name (`drone()->view->templateGlobal()` alias)
- [`view()`](https://github.com/shayanderson/drone#views) - `drone->view` alias

#### Settings
Drone can run without changing the default settings, however, the default settings should be changed in the `_app/com/xap.bootstrap.php` file when Drone is used in a production environment:
```php
// turn debug mode off - this will prevent unwanted output in a production environment
\Drone\Registry::set(\Drone\Core::KEY_DEBUG, false);

// turn off backtrace in log - this should only be used in a development environment
\Drone\Registry::set(\Drone\Core::KEY_ERROR_BACKTRACE, false);

// turn on logging of errors in the default Web server log file
\Drone\Registry::set(\Drone\Core::KEY_ERROR_LOG, true);
```

#### Run Application
The last call in the `index.php` file should run the application:
```php
drone()->run();
```
Nothing should happen after this call as the output buffer has already ended.

To setup an application response simply create a new controller file in the `_app/mod` directory, for example `_app/mod/hello-world.php`:
```php
// display view template with auto template name
view()->display();
```
Next, create a view template `_app/tpl/hello-world.tpl`:
```html
Hello world
```
Finally, visit your Web application with request `/hello-world.htm` in a browser and you should see the `Hello world` text.

## Routes
There are two types of routes in Drone: *static* and *mapped*.

The Drone routing workflow is:

1. Check match for mapped route
2. Check for static route
3. Trigger 404 handler

#### Static Routes
Static routes require no mapping and instead rely on static file paths. For example, the application request `/hello-world.htm` will search for the controller file `_app/mod/hello-world.php`.

<blockquote>A missing static route file will trigger the 404 error handler</blockquote>

<blockquote>Static route lookups happen <i>after</i> mapped route lookups</blockquote>

#### Mapped Routes
Mapped routes require mapping in the `_app/com/xap.bootstrap.php` file, example:
```php
drone()->route([
	'/item-view' => 'item/view',
	'/item-delete/:id' => 'item/delete'
]);
```
In the example above Drone will map the request `/item-view.htm` to the controller file `_app/mod/item/view.php`. The next array element will map the request `/item-delete/14.htm` to the controller file `_app/mod/item/delete.php`, and Drone will map the route param `id` to value `14`.

Here is another example that uses Drone's `Controller` class logic:
```php
drone()->route([
	'/user/:id' => 'user->view',
	'/user/:id/delete' => 'user->delete'
]);
```
In this example the request `/user/5.htm` will be mapped to the controller file `_app/mod/user.php` with the route param `id` set to `5`. In this case the controller file `_app/mod/user.php` will need to contain the `Controller` class with the public method `view` (the action), for example:
```php
class Controller extends \Drone\Controller
{
	public function view()
	{
		$this->id = param('id'); // get route param value (5)
	}
}
```
Likewise the request `/user/5/delete.htm` will be mapped to the controller file `_app/mod/user.php` with the route param `id` set to `5` and call the `Controller` public class method `delete`.

Custom controller class names can be used, for example:
```php
drone()->route([
	'/user/:id' => 'user->\UserController->view',
	'/user/:id/delete' => 'user->\UserController->delete'
]);
```
Now the request `/user/5.htm` will be mapped to the controller file `_app/mod/user.php` and will need to contain the `UserController` class with public method `view`.

<blockquote>A missing <code>Controller</code> class will trigger the 500 error handler</blockquote>

<blockquote>A missing <code>Controller</code> action (class public method) will trigger the 500 error handler</blockquote>

<blockquote>Mapped route lookups happen <i>before</i> static route lookups</blockquote>

##### Optional Route Parameters
*Optional* route params can be used, for example:
```php
drone()->route([
	'/product/:category_id/:id?' => 'product->view',
]);
```
Now the request `/product/14.htm` will route to the controller file with the param `category_id` with value `14`. Likewise, the request `/product/14/5.htm` will route to the controller file with the params `category_id` with value `14`, and the `id` param with value `5`, for example:
```php
$cat_id = param('category_id');
if(param('id') !== null) // test if exists
{
	$id = param('id');
}
```

##### Wildcard Route Parameters
*Wildcard* route params can be used, for example:
```php
drone()->route([
	'/user/*' => 'user->view',
]);
```
Now the request `/user/a/b/c.htm` will be mapped to the controller file with action and all params will become available, for example:
```php
$params = param(0, 1, 2); // ['a', 'b', 'c']
// or set a single value
$param = param(1); // 'b'
```
Wildcard route param labels can also be used, for example
```php
drone()->route([
	'/product/*(:category/:subcat/:id)' => 'product->view',
]);
```
Now the params can be accessed using the param labels, for example the request `/product/category1/category2/4.htm` would be used like:
```php
$category = param('category'); // 'category1', alias: param(0)
$subcategory = param('subcat'); // 'category2', alias: param(1)
$id = param('id'); // '4', alias: param(2)
```

> *Duplicate Content Protection* <br />
A request mapped to a route with *optional* or *wildcard* params *must* end in '/' when not using params in the request, for example route `/route.htm` would result in a 404 error, but `/route/` will work.

> Likewise, a request mapped to a route with *optional* or *wildcard* route params must *not* end in '/' when using params, for example route `/route/x/y/z/` would result in a 404 error, but `/route/x/y/z.htm` will work.

##### Route Files
A *route file* can be used for any base route (using the pattern `/[base route]:`), for example:
```php
drone()->route([
	// setup route file for base route
	'/product:' => PATH_ROOT . '_app/com/route/product.php'
]);
```
The `PATH_ROOT . '_app/com/route/product.php'` file should return an array of mapped routes, for example:
```php
return [
	'/product/:id/delete' => 'product->delete',
	'/product/:id' => 'product->view'
];
```
> A *route file* matched route will override any mapped route following the route file entry

## Controllers
Controllers are files that may or may not contain a `Controller` class depending on if the requested route is mapped, and mapped with an action (see [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes)).

An example of a simple controller file is the default `_app/mod/index.php` controller:
```php
// log example
logger()->debug('Index controller start');

// set params
view()->drone_ver = \Drone\Core::VERSION;
view()->drone_params = drone()->getAll();

// display view (displays _app/tpl/index.tpl when no template name)
view()->display();

// log example
logger()->debug('Index controller end');
```
In the controller file several helper functions are called: `logger()` and `view()`. These helper functions access Drone core components (in this case `drone()->log` and `drone()->view`). So instead of calling `drone()->log->debug('x')` a helper function can be used (see more [Helper Functions](https://github.com/shayanderson/drone#helper-functions)).

View variables can be set using the `view()` helper function, which accesses the `\Drone\View` object, for example:
```php
view()->my_var = 'my value';
```
Now the variable `$my_var` is accessible from the view template file.

> Controller files should never output anything (and outputs will be flushed when debug mode is off), instead output from view template files

#### Controller Class
When a route is mapped with an action (for example: `'/my/route' => 'route->action'`) the controller file *must* contain a `Controller` class (or a custom controller class if used, for example `UserController`, see [Mapped Routes](https://github.com/shayanderson/drone#mapped-routes)), otherwise a 500 error will be triggered.

Here is an example of a simple `Controller` class in a controller file:
```php
class Controller extends \Drone\Controller
{
	public function action()
	{
		logger()->debug('Controller action called');

		// action logic here
	}
}
```
In the mapped route example above the class method `action()` will be called for the request `/my/route.htm`.

Any *public* properties in the `Controller` class will be available as variables in view template files, for example in the `Controller` class example above we could add:
```php
...
	public function action()
	{
		$this->my_class_var = 'my value';

		// action logic here
	}
...
```
Now the variable `$my_class_var` is accessible from the view template file, *unless* the same variable name has been set using the `view()->[var name]` logic, which will override class variables.

> The `Controller` class can use two special methods:
> - `__before()` - called *before* the controller action method is called
> - `__after()` - called *after* the controller action method is called

<blockquote>Mapped route params are accessible from the <code>param()</code> helper function (example: <code>param('id')</code>)</blockquote>

> It is recommended that `Controller` classes extend the `\Drone\Controller` class, this is because the `\Drone\Controller` will automatically deny static requests to the controller file (or mapped requests with no action).

## Views
The Drone `\Drone\View` object handles all view logic like view variables and template path formatting.

The view object is accessible via the `view()` helper function.

View variables (or properties) are set in controller files, for example:
```php
view()->my_var = 'my value';
view()->another_var = 'another value';
view()->display(); // display template file
```

The `view()->display()` method is used to display a template file. If `view()->display()` is not called then no view will be displayed (no output buffer).

When the view display method is called from the controller it will automatically display a similarly named template file, for example, the controller file `_app/mod/test-controller.php` will display the `_app/tpl/test-controller.tpl` when `view()->display()` is called.

To assign a custom view template file use a template name, for example:
```php
// display template file '_app/tpl/my-dir/my-template.tpl'
view()->display('my-dir/my-template');
```
Also, a controller template path can be set using the `view()->displayPath()` method:
```php
// set controller template path '_app/tpl/my-dir/'
view()->displayPath('my-dir');
// display template file '_app/tpl/my-dir/my-template.tpl'
view()->display('my-template');
```

> Other useful view methods:
> - `view()->clearProperties()` - clears all view variables/properties
> - `view()->getProperties()` - get array of all view variables/properties

#### View Templates
Now the variables set in the view example above are accessed in the view template file like:
```html+php
Value for 'my_var' is: <?=$my_var?> <br />
Value for 'another_var' is: <?=$another_var?>
```
Which would output:
```html
Value for 'my_var' is: my value
Value for 'another_var' is: another value
```

*Template global* files can be included using the `template_global()` helper function, for example:
```html+php
<?php include template_global('header'); ?>
Some body text
<?php include template_global('footer'); ?>
```
This example includes the global template files `_app/tpl/_global/header.tpl` and `_app/tpl/_global/footer.tpl`

> The helper function `template()` can be used to include non-global template files

##### Global View Header and Footer Templates
Global view *header* and *footer* templates can be used, for example in the `_app/com/xap.bootstrap.php` file a *before* hook can be created to set the templates:
```php
drone()->hook(\Drone\Core::HOOK_BEFORE, function(){
	view()->templateHeader(template_global('header'));
	view()->templateFooter(template_global('footer'));
});
```
Basically this tells the view to automatically include the global template files for `header` (`_app/tpl/_global/header.tpl`) and `footer` (`_app/tpl/_global/footer.tpl`). Now the `include template_global('header')` and `include template_global('footer')` lines are not required in the view template file (like in the [View Templates](https://github.com/shayanderson/drone#view-templates) example above).
> If a view template file does not require the global header and footer template files, simply turn off the global includes in the *controller* file like:
```php
view()->templateHeader();
view()->templateFooter();
```

## Logging
The `\Drone\Logger` object is used for logging and accessed using the `logger()` helper function.

Log a simple application message example:
```php
logger()->debug('My log message'); // log message with debug level
```
A category can also be used when logging a message, for example:
```php
// log message with category 'account'
logger()->debug('User login successful', 'account');
```
> The default category `app` is used when no category has be set

Data (as an array) can also be passed to the log handler using the `data()` method:
```php
logger()->data([1, 2, 3]);
logger()->debug('My message with data');
```
Now the message will include the data as a flattened string.

#### Log Levels
Drone uses the following logging methods for the logging levels: *debug*, *warn*, *error* and *fatal*:

- `logger()->debug()` - debugging messages
- `logger()->warn()` - warning messages
- `logger()->error()` - error messages (non-fatal)
- `logger()->fatal()` - fatal error messages

> The `logger()->trace` method is used by the framework for debugging purposes

#### Log Configuration
Logging configuration is done in the `_app/com/xap.bootstrap.php` file.

To set the global *log level* use:
```php
drone()->log->setLogLevel(\Drone\Logger::LEVEL_DEBUG);
```
This means only messages with the *debug* level or higher will be logged.

To set a log file where log messages will be outputted to use something like:
```php
drone()->log->setLogFile('_app/var/drone.log');
```
This will output log messages to the log file `_app/var/drone.log`.

> Using a log file is *not* recommended for production environments

#### Custom Log Handler
Setting a custom log handler is simple, for example:
```php
drone()->log->setLogHandler(function($message, $level, $category, $data) {
	xap('drone_log:add', ['message' => $message, 'level' => $level,
		'category' => $category, 'data' => serialize($data)]);
	return true;
});
```
In the above example a custom log handler has been set and allows the log messages to be saved in the database table *drone_log* using the [`xap()`](https://github.com/shayanderson/drone#database-handling) database function.

If a custom log handler is set and returns boolean value `false` Drone will continue on with the default logging logic (caching log messages and writing to a log file if configured), however, if `true` is returned by the log handler Drone will stop the default logging processes.

> Other useful logger methods:
> - `logger()->get()` - gets log as array
> - `logger()->getString()` - gets log as string
> - `logger()->setDateFormat()` - set log message date format

## Error Handling
Errors can be triggered using the `error()` helper function, here is an example:
```php
if($something_bad)
{
	// trigger 500 error handler
	error('Something bad happened');
}
```
Errors can also be triggered using error codes, for example a *404 Not Found*:
```php
error(404); // trigger 404 error handler
```
Or, use custom error codes (cannot be `403`, `404` or `500` as these are used by Drone):
```php
error(100, 'My custom error'); // trigger 100 error handler
```

<blockquote>A custom error code will attempt to trigger a custom error handler, if the handler is not found the <code>500</code> error handler will be triggered</blockquote>

<blockquote>Errors are automatically sent to the <code>\Drone\Logger</code> object to be logged</blockquote>

#### Setting Error Handlers
By default at least three errors handlers should be set in the `_app/com/xap.bootstrap.php` file: a *default* error handler, a *404* error handler and a *500* error handler, example:
```php
drone()->error(function($error) { echo '<div style="color:#f00;">' . $error
	. '</div>'; });
drone()->error(404, function() { drone()->run('error->\ErrorController->_404'); });
drone()->error(500, function() { drone()->run('error->\ErrorController->_500'); });
```

The *default* error handler will be called when errors are triggered inside the application (like E_USER_ERROR, E_USER_WARNING, etc.). This happens because of the default Drone error handler.
> The default Drone error handler does not need to be changed, but it can be changed using:
> ```php
> drone()->set(\Drone\Core::KEY_ERROR_HANDLER, ['\My\Class', 'errorHandlerMethod']);
> ```

Custom error handlers can also be set, for example:
```php
drone()->error(100, function() { drone()->run('error->\ErrorController->_100'); }
```
Now if a `100` error is triggered the handler would call the controller action method `_100()` in the `_app/mod/error.php` controller file.

> The `error_last()` helper function can be used to get the last error message.

## Core Methods
There are Drone core (`\Drone\Core`) methods that are available for application use.

#### Registry
The \Drone\Registry class is useful for global variables and objects, here is an example:
```php
use \Drone\Registry;
...
Registry::set('user', new \User($user_id)); // set key/value
...
if(Registry::has('user')) // check if key exists
{
	if(Registry::get('user')->isActive()) // get key value
	{
		// or $user = &Registry::get('user'); // as reference
	}
}
...
Registry::clear('user'); // unset key
```

> Drone uses some params for internal use, these param keys all share the prefix `__DRONE__.`, for example a Drone param is `__DRONE__.error.backtrace`

#### Route Parameters
Route parameters, or *route params*, are used to extract route param values. For example, for the mapped route `'/route/:id' => 'route->action'` the param `id` will be available as a route param, controller example:
```php
$id = param('id'); // get route param 'id'
```
To verify a route param exists check for the boolean value `false`:
```php
if(param('id') === null)
{
	// the param 'id' does not exist
}
```
Multiple params can also be fetched, for example:
```php
$params = param('id', 'name'); // ['id' => 'x', 'name' => 'y']
```
All params can be fetched using:
```php
$params = param(null); // ['id' => 'x', 'name' => 'y', ...]
// or count all params:
if(count(params(null)) > 2) // more than 2 params
```
> [*Optional*](https://github.com/shayanderson/drone#optional-route-parameters) and [*wildcard*](https://github.com/shayanderson/drone#wildcard-route-parameters) route params are also available

#### Hooks
Hooks can be used to initialize, import logic into or finalize an application. The three types of hooks are: *before* (triggered before the controller file is imported), *middle* (triggered after action is called and before template is loaded), and *after* (triggered after the controller file is imported, the action is called and template file is imported).

Example of *before*, *middle* and *after* hooks set in `_app/com/xap.bootstrap.php`
```php
// call function to init application logic
drone()->hook(\Drone\Core::HOOK_BEFORE, function() { initAppLogic(); });
// import some app front logic
drone()->hook(\Drone\Core::HOOK_MIDDLE,
	function() { include PATH_ROOT . '_app/com/front.php'; });
// print Drone log
drone()->hook(\Drone\Core::HOOK_AFTER, function() { pa('', 'Log:', drone()->log->get()); });
```

> For controller level hooks (special methods `__before()` and `__after`) see [Controller Class](https://github.com/shayanderson/drone#controller-class)

Hooks can also be set to require a hook file instead of using a callable. Simply use a `string` instead of `callable`, for example:
```php
// hook to require file for front logic
drone()->hook(\Drone\Core::HOOK_MIDDLE, PATH_ROOT . '_app/com/hook/middle.php');
```
> Note: *before* and *middle* hook files will be in the scope of view variables (meaning a variable `$var` set in a hook file will be accessible in the view template as `$var`), but *after* hook files are outside that scope.

#### Redirect
Redirection to another location can be done in controller files use the `redirect()` (`\Drone\Core->redirect()` alias) function, for example:
```php
redirect('/new/route.htm'); // redirect
```
If the redirection is a permanent (301) redirect use:
```php
redirect('/forever/route.htm', true); // redirect with 301
```

#### Headers
HTTP headers can be sent in controller files using the `drone()->header()` header method, for example:
```php
drone()->header('Cache-Control', 'no-cache, must-revalidate');
```
> For redirection to another location use the helper function [redirect()](https://github.com/shayanderson/drone#redirect) instead of the header method

#### Timers
The `drone()->timer()` method can be used for timers, for example in a controller file:
```php
$elapsed_time = drone()->timer(); // 0.00060
// do some heavy lifting
$elapsed_time = drone()->timer(); // 0.00071
```
Also custom timers can be used, for example:
```php
drone()->timer('my_job'); // start timer at 0
// do some heavy lifting
$elapsed_time = drone()->timer('my_job'); // 0.00014
```

#### Stop Application
If the application needs to be stopped in a controller file it can be done manually:
```php
drone()->stop(); // the application will stop
```
<blockquote>The <code>drone()->stop()</code> method does not need to be called unless a forced stop is desired (Drone will automatically call <code>drone()->stop()</code> after executing the request, triggering an error or redirecting)</blockquote>

<blockquote><i>After</i> hooks are triggered during a forced application stop, but the <code>Controller</code> method <code>__after()</code> will not be called</blockquote>

## Request Variables
Request variables can be accessed using the `request()` helper function (which uses the `\Drone\Request` object), for example:
```php
$name = request()->get('name'); // get value from GET variable 'name'
```
Methods used to get request variables:
- `request()->cookie()` - `$_COOKIE` alias
- `request()->get()` - `$_GET` alias
- `request()->evn()` - `$_ENV` alias
- `request()->file()` - `$_FILES` alias
- `request()->get()` - `$_GET` alias
- `request()->post()` - `$_POST` alias
- `request()->request()` - `$_REQUEST` alias
- `request()->server()` - `$_SERVER` alias

> *Get* methods can also fetch multiple variables using an array, example:
```php
// ['var1' => x, 'var2' => y, 'var3' => z]
$vars = request()->get(['var1', 'var2', 'var3']);
```

Methods used to check if request variables exist:
- `request()->hasCookie()`
- `request()->hasFile()`
- `request()->hasGet()`
- `request()->hasPost()`
- `request()->hasRequest()`

> *Has* methods can be used to check if multiple variables exists using an array, example:
```php
if(request()->hasPost(['var1', 'var2', 'var3'])) // true or false
```

Methods used to remove request variables:
- `request()->removeCookie()`
- `request()->removeGet()`
- `request()->removePost()`
- `request()->removeRequest()`

Request variable values can be globally sanitized using the `request()->filter()` method, for example:
```php
// auto trim all GET + POST variable values
request()->filter(\Drone\Request::TYPE_GET | \Drone\Request::TYPE_POST,
	function($v) { return data()->filterSanitize(trim($v)); });
```

Cookies are easy to set using:
```php
// set cookie 'my_cookie' that will expire in 10 days
request()->setCookie('my_cookie', 'cookie value', '+10 days');
```

> Other useful request methods:
> - `request()->getHost()`
> - `request()->getIpAddress()`
> - `request()->getMethod()` - get the request method
> - `request()->getPort()`
> - `request()->getProtocol()`
> - `request()->getQueryString()`
> - `request()->getReferrer()`
> - `request()->getSchema()`
> - `request()->getUri()`
> - `request()->isAjax()` - check if Ajax request
> - `request()->isPost()` - check if POST request method
> - `request()->isSecure()` - check if HTTPS request

## Session Handling
Sessions are handled with the `\Drone\Session` object and accessed using the `session()` helper function, example:
```php
session()->set('my_key', 'my value');
...
if(session()->has('my_key'))
{
	$key = session()->get('my_key');
}
```

> The session handler will automatically start a session (if not already started) when the `session()` helper function is used in the application

Using array values in sessions are simple:
```php
session()->add('user', 'id', $user_id);
...
if(session()->has('user', 'id'))
{
	$user_id = session()->get('user', 'id');
}
```

> Other useful session methods:
> - `session()->clear()` - clear a session variable
> - `session()->count()` - used to get count of session array variable
> - `session()->destroy()` - destroy a session
> - `session()->flush()` - flush all session variables
> - `session()->getId()` - get session ID
> - `session()->isArray()` - check if session variable is array
> - `session()->isSession()` - check if session has been started
> - `session()->newId()` - regenerate session ID

#### Flash Messages
Flash messages are simple session messages that last only until they are used. The `\Drone\Flash` object handles flash messages and can be accessed using the `flash()` helper function, example:
```php
// in a controller a validation error is set as a flash message
flash('error', 'Please enter a valid email address');
```
Next, in the view template file call the flash message:
```html+php
<?=flash('error')?>
```
The flash message will only appear once, and be destroyed immediately after. This is very helpful for displaying one-time client messages and errors.

> When the `flash()` helper function is called a session with be started automatically if required

The true power of flash messages is the use of templates, for example in the `_app/com/xap.bootstrap.php` file set a flash message template:
```php
// sets template for flash message group 'error'
\Drone\Flash::template('error', '<div class="error">{$message}</div>');
```
Then set the flash message in the controller:
```php
flash('error', 'Please enter a valid email address');
```
Now in the view template when the `flash()` helper function is called with the group `error` the template is applied:
```html+php
<?=flash('error')?>
```
This will output the HTML:
```html
<div class="error">Please enter a valid email address</div>
```
Also multiple group messages can be used:
```php
// setup template to handle multiple messages
// the 2nd param is the group template, the 3rd param is the message template
\Drone\Flash::template('error', '<div class="error">{$message}</div>',
	'{$message}<br />');
// set multiple validation errors in group 'error'
flash('error', 'Please enter your name');
flash('error', 'Please enter a valid email address');
```
Now output the errors:
```html+php
<?=flash('error')?>
```
The output HTML:
```html
<div class="error">Please enter your name<br />Please enter a valid email address
<br /></div>
```
Also the message template can be used without a group template, for example if every message should be in a separate `<div>` tag:
```php
\Drone\Flash::template('error', null, '<div class="error">{$message}</div>');
```
Now using the same errors above the HTML output would be:
```html
<div class="error">Please enter your name</div>
<div class="error">Please enter a valid email address</div>
```

> Other useful flash methods:
> - `flash()->clear()` - clear a flash message
> - `flash()->flush()` - flush all flash messages
> - `flash()->has()` - check if flash message exists

## Data Handling
Drone supports data handling: filtering, formatting and validation using the `data()` helper function (which uses the `\Drone\Data` object).

#### Filter
Data can be filtered/sanitized using the `data()->filter*` helper function syntax, for example:
```php
// trim value
$trimmed = data()->filterTrim(' my value '); // 'my value'
```

Some filter methods use arguments (or *params*), for example:
```php
// strip non-word characters, but allow whitespaces
$words = = data()->filterWord('my value!', true); // 'my value'
```

Available filters are:
- `filterAlnum(value, allow_whitespaces)` - strip non-alphanumeric characters
- `filterAlpha(value, allow_whitespaces)` - strip non-alpha characters
- `filterDate(value)` - strip non-date characters
- `filterDateTime(value)` - strip non-date/time characters
- `filterDecimal(value)` - strip non-decimal characters
- `filterEmail(value)` - strip non-email characters
- `filterHtmlEncode(value)` - encode HTML special characters
- `filterNumeric(value)` - strip non-numeric characters
- `filterSanitize(value)` - strip tags
- `filterTime(value)` - strip non-time characters
- `filterTrim(value)` - trim spaces
- `filterUrlEncode(value)` - encode URL
- `filterWord(value, allow_whitespaces)` - strip non-word characters (same as character class '\w')

#### Format
Data can be formatted using the `data()->format*` helper function syntax, for example:
```php
// format number to currency
$currency = data()->formatCurrency(5); // '$5.00'
```

Some formatter methods use arguments (or *params*), for example:
```php
// format number to currency with custom currency format
$currency = data()->formatCurrency(5, '$%0.2f USD'); // '$5.00 USD'
```

Available formats are:
- `formatBase64UrlDecode(value)`
- `formatBase64UrlEncode(value)`
- `formatByte(value, characters)`
- `formatCurrency(value, format)`
- `formatDate(value, format)`
- `formatDateTime(value, format)`
- `formatLower(value)`
- `formatTime(value, format)`
- `formatTimeElapsed(time_elapsed, characters)`
- `formatUpper(value)`
- `formatUpperWords(value)`

#### Validate
Data validation can be done using the `data()->validate*` helper function syntax, for example:
```php
// validate email value
if(!data()->validateEmail('bad-email@'))
{
	// warn user
}
```

Some validator methods use arguments (or *params*), for example:
```php
// validate string length (minimum 4, maximum 50)
if(!data()->validateLength('my string', 4, 50))
{
	// warn
}
```

Available validators are:
- `validateAlnum(value, allow_whitespaces)` - value is alphanumeric characters
- `validateAlpha(value, allow_whitespaces)` - value is alpha characters
- `validateBetween(value, min, max)` - value between min and max values
- `validateContains(value, contain_value, is_case_insensitive)` - value contains value
- `validateContainsNot(value, contain_not_value, is_case_insensitive)` - value does not contain value
- `validateDecimal(value)` - value is decimal
- `validateEmail(value)` - value is email
- `validateIpv4(value)` - value is IPv4 address
- `validateIpv6(value)` - value is IPv6 address
- `validateLength(value, min, max, exact)` - value is min length, or under max length, or between min and max lengths
- `validateMatch(value, compare_value, is_case_insensitive)` - value is match to value
- `validateNumeric(value)` - value is numeric
- `validateRegex(value, pattern)` - value is Perl-compatible regex pattern
- `validateRequired(value)` - value exists (length > 0)
- `validateUrl(value)` - value is URL
- `validateWord(value, allow_whitespaces)` - value is word (same as character class '\w')

> Arrays can be passed to data methods, for example:
```php
$values = [' a ', ' b ', ' c '];
$trimmed = array_map([data(), 'filterTrim'], $values) ); // ['a', 'b', 'c']
```

## Database Handling
Drone uses the [Xap](https://github.com/shayanderson/xap) MySQL rapid development engine for database handling. Xap is an optional library and must be installed.

Get Xap options:

- Git clone: `git clone https://github.com/shayanderson/xap.git`
- Subversion checkout URL: `https://github.com/shayanderson/xap/trunk`
- Download [ZIP file](https://github.com/shayanderson/xap/archive/master.zip)

To install Xap put the bootstrap file in `_app/com/xap.bootstrap.php`, put the `lib/Xap` directory in the `_app/lib` directory, and include the `_app/com/xap.bootstrap.php` in the `_app/com/xap.bootstrap.php` file.

<br /><br />

More Drone documentation can be found in the [docs directory](https://github.com/shayanderson/drone/tree/master/docs)