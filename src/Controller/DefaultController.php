<?php

namespace App\Controller;

use App\Helpers\ElasticsearchServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class DefaultController extends Controller
{

    public function indexAction(Request $request)
    {
      $i18n = $this->container->get('hevea_i18n_helper')->dumpDictionary();
      return $this->render('global.html.twig', array('i18n' => json_encode($i18n)));
    }
}
