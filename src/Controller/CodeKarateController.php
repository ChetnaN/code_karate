<?php

namespace Drupal\code_karate\Controller;

use Drupal\Core\Controller\ControllerBase;
use Drupal\node\Entity\Node;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * CodeKarateController controller.
 */
class CodeKarateController extends ControllerBase {

  /**
   * {@inheritdoc}
   */
  public function get($siteapikey, $nid) {
    $sak = \Drupal::state()->get('siteapikey') ?: ''; // Getting siteapikey system variable if set, else setting it to NULL
    $no_access = 0; // If no_access is 0, then only access will be granted, if it will be set to 1, then "Access denied" will show up

    $response = new JsonResponse();
    if($siteapikey == $sak) { // if $siteapikey value from URL and siteapikey system variable is same, then node is loaded by the node is passed in the URL
      $node = Node::load($nid);

      if(!empty($node)) {
        $type = $node->bundle();
        if($type == "page") { // If node is of content type "page" than only it's data will be returned as JSON.
          $data = array(
            'node' => array(
              'title' => $node->get('title')->getValue()[0]['value'],
              'body' => $node->get('body')->getValue()[0]['value'],
              'type' => $type,
            )
          );
        }
        else { // if node is not of type page
          $no_access = 1;
          
        }
      }
      else { // if no node with node id given
        $no_access = 1;
      }

    }
    else { // if $siteapikey value from URL and siteapikey system variable do not match
      $no_access = 1;     
    }

    if($no_access == 1) { // if due to any reason above the access is not granted
      $data = array('code' => 403 , 'message' => 'Access Denied');
      $response->setData($data);
    }
    else { // else return node JSON
      $response->setData($data);
      
    }
    return $response;
  }
}