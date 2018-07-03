<?php

namespace App\Controller;

use App\Helpers\ElasticsearchServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class DashboardController extends Controller
{

    public function infoRestAction(Request $request)
    {
      try {
        /** @var ElasticsearchServer $server */
        $server = $this->container->get('hevea_elasticsearch_server');
        return new Response(json_encode($server->getServerInfo(), JSON_PRETTY_PRINT), 200, array('Content-type' => 'application/json; charset=utf-8'));
      }
      catch(\Exception $ex){
        return new Response(json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT), 500, array('Content-type' => 'application/json; charset=utf-8'));
      }
    }
}
