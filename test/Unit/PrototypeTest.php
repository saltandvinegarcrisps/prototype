<?php

class PrototypeTest extends PHPUnit_Framework_TestCase {

	public function testValue() {
		$this->assertEquals('bar', value(array('foo' => 'bar'), 'foo'));
	}

	public function testValueWithMissingKey() {
		$this->assertEquals('baz', value(array('foo' => 'bar'), 'qux', 'baz'));
	}

	public function testUri() {
		$result = uri(array('REQUEST_URI' => '/foo'));

		$this->assertEquals('/foo', $result);
	}

	public function testUriDefault() {
		$result = uri(array());

		$this->assertEquals('/', $result);
	}

	public function testUriWithQueryString() {
		$result = uri(array('REQUEST_URI' => '/foo?bar=baz'));

		$this->assertEquals('/foo', $result);
	}

	public function testOptionGet() {
		$storage = array('foo' => 'bar');
		$result = option('foo', null, $storage);

		$this->assertEquals('bar', $result);
	}

	public function testOptionSet() {
		$storage = array();

		option('foo', 'bar', $storage);

		$this->assertEquals($storage, array('foo' => 'bar'));
	}

	public function testRoutes() {
		$expected = array();
		$result = routes();

		$this->assertEquals($expected, $result);

		option('routes', array('/' => function() {}));

		$result = routes();

		$this->assertEquals(1, count($result));
	}

	public function testRoute() {
		$route = function() {};

		route('/', $route);

		$result = routes();

		$this->assertEquals($route, $result['/']);
	}

	public function testRender() {
		// create temp file
		$file = tempnam(sys_get_temp_dir(), __FUNCTION__);
		file_put_contents($file, 'Hello <?php echo $name; ?>.');

		$result = render($file, array('name' => 'World'));

		$this->assertEquals('Hello World.', $result);

		unlink($file);
	}

	public function testRenderRelativeFile() {
		// create temp file
		option('view_dir', __DIR__);

		$file = 'test.phtml';
		file_put_contents(__DIR__.'/'.$file, 'Hello <?php echo $name; ?>.');

		$result = render($file, array('name' => 'World'));

		$this->assertEquals('Hello World.', $result);

		unlink(__DIR__.'/'.$file);
	}

	public function testRenderMissingFile() {
		$this->setExpectedException('InvalidArgumentException');
		render('missing_file');
	}

	public function testRun() {
		route('/', function() {
			return 'Hello World';
		});

		$result = run();

		$this->assertEquals('Hello World', $result);
	}

	public function testRunWithoutRouteOrError() {
		$this->setExpectedException('ErrorException');

		option('routes', array());

		run();
	}

	public function testRunWith404() {
		option('routes', array());

		option('error_404', function() {
			return '404';
		});

		$result = run();

		$this->assertEquals('404', $result);
	}

	public function testRunWithMap() {
		option('routes', array());

		option('auto_map', function() {
			return 'mapped';
		});

		$result = run();

		$this->assertEquals('mapped', $result);
	}

	public function testViewWithoutDir() {
		$storage = null;
		option('view_dir', null, $storage, true);
		$this->setExpectedException('ErrorException');
		view('/test');
	}

	public function testView() {
		option('view_dir', __DIR__);
		file_put_contents(__DIR__.'/test.php', 'Hello World');
		$result = view('/test');

		$this->assertEquals(__DIR__.'/test.php', $result);
		$this->assertEquals('Hello World', file_get_contents($result));
		unlink(__DIR__.'/test.php');
	}

}