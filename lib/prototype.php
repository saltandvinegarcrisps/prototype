<?php

/**
 * Get the currrent URI string
 *
 * @param array
 * @return string
 */
function uri($server = null) {
	if(null === $server) {
		$server = $_SERVER;
	}

	// get requested uri
	$uri = value($server, 'REQUEST_URI', '/');

	// strip query string
	if($pos = strpos($uri, '?')) {
		$uri = substr($uri, 0, $pos);
	}

	return $uri;
}

/**
 * Get a value from array
 *
 * @param array
 * @param string/integer
 * @param mixed
 * @return mixed
 */
function value($array, $key, $default = null) {
	return isset($array[$key]) ? $array[$key] : $default;
}

/**
 * Get/Set options
 *
 * @return mixed
 */
function option() {
	static $options = array();

	$args = func_get_args();
	$num = func_num_args();

	if($num === 1) {
		return value($options, $args[0]);
	}

	if($num === 2) {
		$options[$args[0]] = $args[1];
	}
}

/**
 * Get defined routes
 *
 * @return array
 */
function routes() {
	$routes = option('routes');

	if(null === $routes) {
		$routes = array();
	}

	return $routes;
}

/**
 * Define a route
 *
 * @param string
 * @param object
 */
function route($path, Closure $route = null) {
	$routes = routes();

	if(null !== $route) {
		$routes[$path] = $route;

		option('routes', $routes);
	}

	return value($routes, $path);
}

/**
 * Render a view
 *
 * @param string
 * @param array
 * @return string
 */
function render($file, array $vars = array()) {
	// try relative path
	if( ! is_file($file)) {
		$file = option('view_dir').'/'.$file;

		if( ! is_file($file)) {
			throw new InvalidArgumentException(sprintf('View file not found "%s"', $file));
		}
	}

	ob_start();

	extract($vars, EXTR_SKIP);

	require $file;

	return ob_get_clean();
}

/**
 * Match the current uri with a route and call it
 */
function run() {
	$uri = uri($_SERVER);
	$route = route($uri);

	if(null === $route) {
		if($error = option('error_404')) {
			return $error();
		}

		throw new ErrorException('Route not found');
	}

	return $route();
}