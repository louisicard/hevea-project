<?php

namespace App\Controller;

use Elasticsearch\Common\Exceptions\NoNodesAvailableException;
use App\Classes\PersistentObject;
use App\Classes\User;
use App\Classes\UserProvider;
use App\Helpers\ElasticsearchServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;

class SecurityController extends Controller
{

    public function loginAction(Request $request)
    {

      try {

        /** @var ElasticsearchServer $esServer */
        $esServer = $this->container->get('hevea_elasticsearch_server');
        $esServer->initStore();

        /** @var AuthenticationUtils $authenticationUtils */
        $authenticationUtils = $this->get('security.authentication_utils');

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        if($error != null) {
          $errorText = get_class($error) == BadCredentialsException::class ? 'login.badCredentials' : $error->getMessage();
        }
        else {
          $errorText = '';
        }

        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        $noCluster = false;

      } catch (NoNodesAvailableException $ex) {
        $lastUsername = '';
        $error = true;
        $noCluster = true;
        $errorText = $ex->getMessage();
      }
      $i18n = json_decode(file_get_contents(__DIR__ . '/../Resources/i18n/fr.json'));
      return $this->render('login.html.twig', array(
        'i18n' => json_encode($i18n),
        'error' => $error,
        'errorText' => $errorText,
        'lastUsername' => $lastUsername,
        'noCluster' => $noCluster,
      ));
    }

  public function logoutAction(Request $request)
  {

  }
}
