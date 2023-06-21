<?php

namespace App\Libraries\Core;

use \Twig\Loader\FilesystemLoader;
use \Twig\Environment;

class Core
{
  /**
   * @var Environment The Twig environment instance.
   */
  protected static Environment $twig;

  /**
   * @var object The current controller instance.
   */
  protected object $currentController;

  /**
   * @var string The current controller being called.
   */
  protected string $currentControllerName = 'Homepages';

  /**
   * @var string The current method being called on the controller.
   */
  protected string $currentMethod = 'index';

  /**
   * @var array The parameters to be passed to the controller method.
   */
  protected array $params = [];

  /**
   * @var mixed The query string from the request.
   */
  public mixed $query = [];

  /**
   * Initializes the Core class.
   */
  public function __construct()
  {
    $this->initTwig();
    $url = $this->getURL();

    $controller = $this->getControllerName($url);

    if (!$controller)
      $this->pageNotFound();

    $this->currentController = $this->loadController($controller);

    if ($this->hasMethod($url)) {
      $this->currentMethod = $url[1];
      unset($url[1]);
    }

    $this->params = $url ? array_values($url) : [];

    $this->callMethod();
  }

  /**
   * Returns the URL segments from the request.
   *
   * @return array An array of URL segments.
   */
  protected function getURL(): array
  {
    $url = isset($_SERVER['REQUEST_URI']) ? rtrim($_SERVER['REQUEST_URI'], '/') : 'homepages/index';

    $this->query = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : '';
    if (!empty($query)) {
      $url .= '?' . $query;
    }

    $url = filter_var($url, FILTER_SANITIZE_URL);

    $url = explode('/', $url);

    // Remove the "show" segment if it exists and the next segment is not empty
    if (isset($url[0]) && $url[0] === 'show' && isset($url[1]) && $url[1] !== '') {
      unset($url[0]);
    }

    return array_slice($url, 1);
  }


  /**
   * Determines the name of the controller to be called based on the URL segments.
   *
   * @param array $url An array of URL segments.
   * @return string The name of the controller to be called.
   */
  protected function getControllerName(array &$url): string|bool
  {
    if (isset($url[0]) && !file_exists('../app/Controllers/' . ucwords($url[0]) . '.php'))
      return false;

    $this->currentControllerName = isset($url[0]) ? ucwords($url[0]) : $this->currentControllerName;

    unset($url[0]);

    if (isset($url[1]) && $url[1] !== '') {
      $this->currentMethod = $url[1];
      unset($url[1]);
    }

    return $this->currentControllerName;
  }

  /**
   * Loads and instantiates the controller class.
   *
   * @param string $controller The name of the controller to be instantiated.
   * @return object The controller instance.
   */
  protected function loadController(string $controller): object
  {
    $controllerName = '\\app\\Controllers\\' . $controller;
    require_once '../app/Controllers/' . $controller . ".php";
    return new $controllerName();
  }

  /**
   * Determines whether the URL segments contain a method name to be called on the controller.
   *
   * @param array $url An array of URL segments.
   * @return bool Whether a method name is present in the URL segments.
   */
  protected function hasMethod(array &$url): bool
  {
    return isset($url[1]) && method_exists($this->currentController, $url[1]);
  }

  /**
   * Calls the method on the controller with the given parameters.
   */
  protected function callMethod(): void
  {
    if (method_exists($this->currentController, $this->currentMethod)) {
      call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
      exit;
    }

    if (method_exists($this->currentController, 'show')) {
      array_unshift($this->params, $this->currentMethod);
      call_user_func_array([$this->currentController, 'show'], $this->params);
      exit;
    }

    $this->pageNotFound();
  }

  /**
   * Initializes the Twig environment.
   */
  protected function initTwig(): void
  {
    $loader = new FilesystemLoader('../resources/views');
    self::$twig = new Environment($loader, [
      'cache' => '../storage/cache',
      'auto_reload' => true,
    ]);
    self::$twig->addExtension(new \App\Support\GlobalFunctionsTwig());
    self::$twig->addGlobal('global', [
      'appUrl' => $_ENV['APP_URL'],
      'appName' => $_ENV['APP_NAME'],
      'isDev' => $_ENV['APP_ENV'] === 'development',
    ]);
  }

  public static function pageNotFound(): void
  {
    header('HTTP/1.0 404 Not Found');
    echo self::$twig->render('errors/404.twig');
    exit;
  }

  /**
   * Returns the Twig environment instance
   * 
   * @return Environment The Twig environment instance.
   */
  public static function getTwig(): Environment
  {
    return self::$twig;
  }
}
