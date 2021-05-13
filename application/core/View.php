<?php

namespace application\core;

class View
{
	public $path;
	public $route;
	public $layout = 'default';

	function __construct($route)
	{
		$this->route = $route;
		$this->path = $route['controller'].'/'.$route['action'];
	}

	public function render($title, $vars = [])
	{
		extract($vars); // преобразование в переменные
		$path = 'application/views/'.$this->path.'.php';
		if (file_exists($path)) {
			ob_start(); // добавление в буфер
			require_once $path;
			$content = ob_get_clean();
			require_once 'application/views/layouts/'.$this->layout.'.php';
		}
	}

	public function redirect($url)
	{
		header('Location: /'.$url);
		exit();
	}

	public static function errorCode($code)
	{
		http_response_code($code);
		$path = 'application/views/errors/'.$code.'.php';
		if (file_exists($path)) {
			require_once $path;
		}
		exit();
	}

	public function message($status, $message)
	{
		exit(json_encode(['status' => $status, 'message' => $message]));
	}

	public function location($url)
	{
		exit(json_encode(['url' => $url]));
	}
}