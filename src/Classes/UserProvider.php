<?php


namespace App\Classes;


use App\Helpers\ElasticsearchServer;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UserProvider implements UserProviderInterface
{
  /**
   * @var ElasticsearchServer
   */
  private $elasticsearchServer;

  public function __construct(ElasticsearchServer $elasticsearchServer) {
    $this->elasticsearchServer = $elasticsearchServer;
  }

  public function loadUserByUsername($username)
  {
    $user = $this->getUser($username);
    if($user == null){
      throw new UsernameNotFoundException();
    }
    return $user;
  }

  public function refreshUser(UserInterface $user)
  {
    return $this->getUser($user->getUsername());
  }

  public function supportsClass($class)
  {
    return $class == User::class;
  }

  /**
   * @param string $username
   * @return User
   */
  private function getUser($username) {
    try {
      return $this->elasticsearchServer->findObject('user', $username);
    }
    catch(\Exception $ex) {
      return null;
    }
  }

}