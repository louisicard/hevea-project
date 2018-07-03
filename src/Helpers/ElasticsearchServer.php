<?php
/**
 * Created by PhpStorm.
 * User: louis
 * Date: 13/12/2017
 * Time: 20:07
 */

namespace App\Helpers;


use Elasticsearch\Client;
use Elasticsearch\ClientBuilder;
use App\Classes\PersistentObject;
use App\Classes\User;

class ElasticsearchServer
{

  /**
   * @var Client
   */
  private $client;

  private $passwordEncoder;

  public function __construct($elasticsearchServerUrl, $passwordEncoder = null) {
    $clientBuilder = new ClientBuilder();
    if(!defined('JSON_PRESERVE_ZERO_FRACTION')){
      $clientBuilder->allowBadJSONSerialization();
    }
    $clientBuilder->setHosts(array($elasticsearchServerUrl));
    $this->client = $clientBuilder->build();
    $this->passwordEncoder = $passwordEncoder;
  }

  public function getServerInfo() {
    return array(
      'server_info' => $this->client->info(),
      'health' => $this->client->cluster()->health(),
      'stats' => $this->client->cluster()->stats(),
    );
  }

  public function getIndicesList() {
    $mappings = $this->client->indices()->getMapping();
    $settings = $this->client->indices()->getSettings();
    $indices = $this->client->indices()->stats()['indices'];
    foreach($indices as $index => $stats) {
      if(isset($settings[$index])) {
        $indices[$index]['settings'] = $settings[$index]['settings'];
      }
      if(isset($mappings[$index])) {
        $indices[$index]['mappings'] = $mappings[$index]['mappings'];
      }
    }
    ksort($indices);
    return $indices;
  }

  public function getIndex($indexName) {
    return $this->client->indices()->getSettings(array('index' => $indexName));
  }

  public function createIndex($indexName, $settings) {
    $params = array(
      'index' => $indexName,
      'body' => array(
        'settings' => $settings,
      )
    );
    return $this->client->indices()->create($params);
  }

  public function deleteIndex($indexName) {
    $params = array(
      'index' => $indexName
    );
    return $this->client->indices()->delete($params);
  }

  public function createMapping($indexName, $mappingName, $mapping) {
    $this->client->indices()->putMapping(array(
      'index' => $indexName,
      'type' => $mappingName,
      'body' => $mapping
    ));
  }

  function getMapping($indexName, $mappingName)
  {
    try {
      $mapping = $this->client->indices()->getMapping(array(
        'index' => $indexName,
        'type' => $mappingName,
      ));
      if (isset($mapping[$indexName]['mappings'][$mappingName])) {
        return $mapping[$indexName]['mappings'][$mappingName];
      } else
        return null;
    } catch (\Exception $ex) {
      return null;
    }
  }

  public function initStore() {
    $indices = array_keys($this->getIndicesList());
    if(!in_array('.hevea', $indices)) {
      $json = json_decode(file_get_contents(__DIR__ . '/../Resources/store_structure.json'), TRUE);
      $this->createIndex('.hevea', $json['index']);
    }
    $mapping = $this->getMapping('.hevea', 'store_item');
    if($mapping == null) {
      $json = json_decode(file_get_contents(__DIR__ . '/../Resources/store_structure.json'), TRUE);
      $this->createMapping('.hevea', 'store_item', array('properties' => $json['mapping']));
    }
    $users = $this->listObjects('user');
    if(empty($users)) {
      $user = new User('admin', array('ROLE_ADMIN'), 'admin@example.com', 'Administrator', array());
      $encoded = $this->passwordEncoder->encodePassword($user, 'admin');
      $user->setPassword($encoded);
      $this->persistObject($user);
    }
  }

  public function search($indexName, $query, $type = '') {
    return $this->client->search(array(
      'index' => $indexName,
      'type' => $type,
      'body' => $query
    ));
  }

  public function persistObject(PersistentObject $o) {
    $params = array(
      'index' => '.hevea',
      'type' => 'store_item',
      'body' => array(
        'name' => $o->getName(),
        'type' => $o::getType(),
        'data' => $o->serialize()
      )
    );
    if($o->getId() != null) {
      $params['id'] = $o->getId();
    }
    $r = $this->client->index($params);
    if(isset($r['_id'])){
      $o->setId($r['_id']);
    }
    $this->client->indices()->flush();
    return $o;
  }

  public function findObject($type, $id) {
    $query = array(
      'query' => array(
        'bool' => array(
          'must' => array(
            array(
              'term' => array(
                'type' => $type
              )
            ),
            array(
              'ids' => array(
                'values' => array($id)
              )
            )
          )
        )
      )
    );
    $r = $this->search('.hevea', $query);
    if(isset($r['hits']['hits'][0])) {
      return unserialize($r['hits']['hits'][0]['_source']['data']);
    }
    else {
      return null;
    }
  }

  public function listObjects($type, $from = 0, $size = 20, $order = 'asc') {
    $query = array(
      'query' => array(
        'term' => array(
          'type' => $type
        )
      ),
      'size' => $size,
      'from' => $from,
      'sort' => array(
        'name.raw' => $order
      )
    );
    $r = $this->search('.hevea', $query);
    $objects = [];
    foreach($r['hits']['hits'] as $hit) {
      $objects[] = unserialize($hit['_source']['data']);
    }
    return $objects;
  }

}