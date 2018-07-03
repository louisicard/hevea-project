<?php
/**
 * Created by PhpStorm.
 * User: louis
 * Date: 27/03/2018
 * Time: 15:40
 */

namespace App\Classes;


abstract class PersistentObject
{

  abstract function getId();
  abstract function setId($id);
  abstract function getName();
  abstract static function getType();
  public function serialize() {
    return serialize($this);
  }

  public static function unserialize($str) {
    return unserialize($str);
  }

}