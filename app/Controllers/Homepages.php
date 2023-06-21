<?php

namespace App\Controllers;

use App\Libraries\Core\Controller;

class Homepages extends Controller
{
  public function index(): void
  {
    $this->setData([
      'title' => 'Landing Page',
    ]);

    $this->view('homepages/index');
  }
}
