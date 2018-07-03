<?php

namespace App\Helpers;


class i18nHelper
{

  private $locale;
  private $json;

  public function __construct($locale) {
    $this->locale = $locale;
    $file = __DIR__ . '/../Resources/i18n/' . $locale . '.json';
    if(file_exists($file)) {
      $this->json = json_decode(file_get_contents($file), TRUE);
    }
    else {
      $this->json = false;
    }
  }

  public function translate($str, $params = []) {
    if(!$this->json) {
      return $str;
    }
    $parts = explode('.', $str);
    $obj = $this->json;
    $r = false;
    foreach($parts as $part) {
      if(isset($obj[$part])) {
        $obj = $obj[$part];
        $r = true;
      }
      else {
        return $str;
      }
    }
    return $r ? $obj : $str;
  }

  public function dumpDictionary() {
    return $this->json;
  }

}