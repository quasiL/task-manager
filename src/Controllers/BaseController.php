<?php

declare(strict_types=1);

namespace App\Controllers;

abstract class BaseController
{
  /**
   * Renders a view within a layout template.
   *
   * @param string $view The name of the view file (without extension).
   * @param array $data An associative array of data to be extracted and passed to the view.
   *
   * @return void
   */
  protected function render(string $view, array $data = []): void
  {
    extract($data);

    ob_start();
    require dirname(__DIR__, 1) . '/views/' . strtolower($view) . '.php';
    $content = ob_get_clean();

    require dirname(__DIR__, 1) . '/views/layout.php';
  }
}
