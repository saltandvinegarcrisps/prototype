# Prototype

For making quick prototype sites not for production.

Simpley include `prototype.php` or `vendor/autoload.php` if your using composer and define some routes to get started.

	//require 'lib/prototype.php';
	// or
	//require 'vendor/autoload.php';

	route('/', function() {
		echo 'Hello World';
	});

	run();

## Using Views

	option('view_dir', __DIR__.'/views');

	route('/', function() {
		echo render('home.php', array(
			'message' => 'Hello World'
		));
	});

## Handling 404 pages

	option('error_404', function() {
		echo 'Page not found';
	});

## Auto map URLs to views

	// /home --> views/home.php
	option('auto_map', true);

Specify your own auto map callabck.

	// /home --> views/home.html
	option('auto_map', function($uri) {
		echo render($uri . '.html');
	});

Auto map example

	// public/index.php
	require 'lib/prototype.php';

	// Inside views:
	// - home.phtml
	// - about.phtml
	// - contact.phtml
	option('view_dir', __DIR__.'/views');

	// /home --> home.phtml
	option('auto_map', true);

	run();