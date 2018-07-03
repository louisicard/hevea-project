<?php

namespace App\Twig;


use App\Helpers\i18nHelper;
use Twig\Extension\AbstractExtension;

class AppExtension extends AbstractExtension
{
  private $i18nHelper;

  public function __construct(i18nHelper $i18nHelper) {
    $this->i18nHelper = $i18nHelper;
  }

  public function getName(){
    return "hevea_extension";
  }

  public function getFilters()
  {
    return array(
      new \Twig_SimpleFilter('translate', array($this, 'translate'))
    );
  }

  public function translate($str, $params = []){
    return $this->i18nHelper->translate($str, $params);
  }

}