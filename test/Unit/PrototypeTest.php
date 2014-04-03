<?php

class PrototypeTest extends PHPUnit_Framework_TestCase {

	public function testValue() {
		$expected = 'bar';
		$result = value(array('foo' => $expected), 'foo');

		$this->assertEquals($expected, $result);
	}

	public function testValueWithMissingKey() {
		$expected = 'baz';
		$result = value(array('foo' => 'bar'), 'qux', $expected);

		$this->assertEquals($expected, $result);
	}

	public function testUri() {
		$expected = 'foo';
		$result = uri(array('REQUEST_URI' => $expected));

		$this->assertEquals($expected, $result);
	}

	public function testUriDefault() {
		$expected = '/';
		$result = uri();

		$this->assertEquals($expected, $result);
	}

	public function testUriWithQueryString() {
		$expected = '/foo';
		$result = uri(array('REQUEST_URI' => '/foo?bar=baz'));

		$this->assertEquals($expected, $result);
	}

	public function testOptionSetGet() {
		$expected = 'bar';
		option('foo', $expected);
		$result = option('foo');

		$this->assertEquals($expected, $result);
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

}