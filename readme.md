# Prototype

For making quick prototype sites not for production.

Simpley include `prototype.php` and define some routes to get started.

	require __DIR__ . '/prototype.php';

	route('/', function() {
		echo 'Hello World';
	});

	run();

## Using Views

	option('view_dir', __DIR__.'/views');

	route('/', function() {
		echo render('home', array(
			'message' => 'Hello World'
		));
	});

## Handling 404 pages

	option('error_404', function() {
		echo 'Page not found';
	});