<?php

namespace App\Controller;

use App\Helpers\ElasticsearchServer;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class IndicesController extends Controller
{

  public function listIndicesAction(Request $request)
  {
    try {
      /** @var ElasticsearchServer $server */
      $server = $this->container->get('hevea_elasticsearch_server');
      return new Response(json_encode($server->getIndicesList(), JSON_PRETTY_PRINT), 200, array('Content-type' => 'application/json; charset=utf-8'));
    }
    catch(\Exception $ex){
      return new Response(json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT), 500, array('Content-type' => 'application/json; charset=utf-8'));
    }
  }

  public function getIndexAction(Request $request, $indexName)
  {
    try {
      /** @var ElasticsearchServer $server */
      $server = $this->container->get('hevea_elasticsearch_server');
      return new Response(json_encode($server->getIndex($indexName), JSON_PRETTY_PRINT), 200, array('Content-type' => 'application/json; charset=utf-8'));
    }
    catch(\Exception $ex){
      return new Response(json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT), 500, array('Content-type' => 'application/json; charset=utf-8'));
    }
  }

  public function putIndexAction(Request $request)
  {
    try {
      /** @var ElasticsearchServer $server */
      $server = $this->container->get('hevea_elasticsearch_server');
      $settings = json_decode($request->getContent(), TRUE);
      $indexName = $request->get('indexName');
      $r = $server->createIndex($indexName, $settings);
      return new Response(json_encode($r,JSON_PRETTY_PRINT), 200, array('Content-type' => 'application/json; charset=utf-8'));
    }
    catch(\Exception $ex){
      return new Response(json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT), 500, array('Content-type' => 'application/json; charset=utf-8'));
    }
  }

  public function deleteIndexAction(Request $request, $indexName)
  {
    try {
      /** @var ElasticsearchServer $server */
      $server = $this->container->get('hevea_elasticsearch_server');
      $r = $server->deleteIndex($indexName);
      return new Response(json_encode($r, JSON_PRETTY_PRINT), 200, array('Content-type' => 'application/json; charset=utf-8'));
    }
    catch(\Exception $ex){
      return new Response(json_encode(array('error' => $ex->getMessage()), JSON_PRETTY_PRINT), 500, array('Content-type' => 'application/json; charset=utf-8'));
    }
  }
}
