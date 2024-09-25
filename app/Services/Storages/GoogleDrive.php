<?php

namespace App\Services\Storages;

use Google\Client;

class GoogleDrive implements IStorage
{

  public function __construct() {
    $client = new Client();
  }
}
