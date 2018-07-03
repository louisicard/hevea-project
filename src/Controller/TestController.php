<?php

namespace App\Controller;

use App\Classes\PersistentObject;
use App\Classes\User;
use App\Classes\UserProvider;
use App\Helpers\ElasticsearchServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class TestController extends Controller
{

    public function testAction(Request $request)
    {
      /** @var ElasticsearchServer $esServer */
      $esServer = $this->container->get('hevea_elasticsearch_server');
      $esServer->initStore();
      $provider = new UserProvider($esServer);
      try {
        $test = $provider->loadUserByUsername('admin');
        dump($test);
      }catch(\Exception $ex) {
        dump($ex);
      }

      /*
      $user = new User('admin', array('ROLE_ADMIN'), 'admin@example.com', 'Administrator', array());
      $encoder = $this->container->get('security.password_encoder');
      $encoded = $encoder->encodePassword($user, 'admin');
      $user->setPassword($encoded);
      $str = $user->serialize();
      dump(User::unserialize($str));
      dump($esServer->persistObject($user));

      dump($esServer->findObject('user', 'admin'));

      dump($esServer->listObjects('user'));*/
      return new Response('', 200);
    }
}
