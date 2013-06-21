<?php
require 'JSON.php'; // Built in json_decode is BROKEN by razor JSON responses

class razor
{
    
    var $apiConfig = array(
      'url' => "http://localhost:8026/razor/api/",
    );

    var $json;
    
    /* ------------------------------------------------------------------------------------------------------- */
    
    function __construct()
    {
        $this->json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
        
    }
    
    /* ------------------------------------------------------------------------------------------------------- */
        
    public function http_get($url, &$results,&$errMsg = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        
        
        $response = $this->json->decode($response);

        //print_r($response);
        
        if($response['http_err_code'] != 200)
        {
                $errMsg = $response['result'];
                return false;
        }
        else
        {
                $results = $response['response'];
                return true;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */
        
    public function http_post($url, $post_data, &$results,&$errMsg = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "json_hash=" . json_encode($post_data));

        $response = curl_exec($ch);

        $response = $this->json->decode($response);

//        print_r($response);

        if($response['http_err_code'] != 201)
        {
                $errMsg = $response['result'];
                return false;
        }
        else
        {
                $results = $response['response'];
                return true;
        }
    }
    
    /* ------------------------------------------------------------------------------------------------------- */

    public function http_delete($url, &$results,&$errMsg = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
        

        $response = curl_exec($ch);

        $response = $this->json->decode($response);

        if($response['http_err_code'] != 202)
        {
                $errMsg = $response['result'];
                return false;
        }
        else
        {
                $results = $response['response'];
                return true;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */
    
    public function get_nodes()
    {
        $results = null;
        $errMsg = null;
        
        $url = $this->apiConfig['url'] . "node/";
        
        if(!$this->http_get($url,$results,$errMsg))
        {
            return false;
        }
        else
        {
            return $results;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function get_tagUUID_by_tag($tag)
    {
        $results = null;
        $errMsg = null;
            
        $url = $this->apiConfig['url'] . "tag/";
        
        if(!$this->http_get($url,$results,$errMsg))
        {
            echo($errMsg);
            return false;
        }
        else
        {
            // Normalize the JSON return object
            foreach($results as $tags)
            {
                $tagInfo = $this->get_tag($tags['@uuid']);
                if($tagInfo['@tag'] == $tag )
                {
                    return $tags['@uuid'];
                }
                
            }
        }
        
        return false;
    }

    
    /* ------------------------------------------------------------------------------------------------------- */

    public function get_tag($tag = "")
    {
        $results = null;
        $errMsg = null;
        
        $url = $this->apiConfig['url'] . "tag/$tag";
        
        if(!$this->http_get($url,$results,$errMsg))
        {
            return false;
        }
        else
        {
            if($tag != "")
            {
                return reset($results);
            }
            else
            {
                return $results;
            }
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function add_tag($args)
    {
        $results = null;
        $errMsg = null;
        
        if(!$args['name'] or !$args['tag']) { return false; }
        
        $url = $this->apiConfig['url'] . "tag";
        
        if(!$this->http_post($url,$args,$results,$errMsg))
        {
            echo($errMsg);
            return false;
        }
        else
        {
            return reset($results);
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function delete_tag($tag)
    {
        $results = null;
        $errMsg = null;
        
        if(!$tag) { return false; }
        
        $url = $this->apiConfig['url'] . "tag/" . $tag;
        
        if(!$this->http_delete($url,$results,$errMsg))
        {
            echo($errMsg);
            return false;
        }
        else
        {
            print_r($results);
            return $results;
        }
    }

    
    /* ------------------------------------------------------------------------------------------------------- */

    public function add_tag_matcher($args)
    {
        $results = null;
        $errMsg = null;
        
        if(!$this->get_tag($args['tag_rule_uuid'])) { return false; }
        
        $url = $this->apiConfig['url'] . "tag/" . $args['tag_rule_uuid'] . "/matcher";

        if(!$this->http_post($url,$args,$results,$errMsg))
        {

            echo($errMsg);
            return false;
        }
        else
        {
            return $results;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function get_tag_matcher($args)
    {
        $results = null;
        $errMsg = null;
        
        if(!$args['tag_rule_uuid']) { return false; }        
        if(!$this->get_tag($args['tag_rule_uuid'])) { return false; }

        if(!$args['key']) { return false; }
        
        $url = $this->apiConfig['url'] . "tag/" . $args['tag_rule_uuid'];

        if(!$this->http_get($url,$results,$errMsg))
        {
            echo($errMsg);
            return false;
        }
        else
        {
            foreach($results as $result)
            {
                foreach($result['@tag_matchers'] as $item)
                {
                    if($item['@key'] == $args['key'])
                    {
                        return $item;
                    }
                }
            }
            
            return false;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function delete_tag_matcher($tag,$tag_matcher)
    {
        $results = null;
        $errMsg = null;
        
        if(!$tag_matcher) { return false; }
        if(!$tag) { return false; }                
        $url = $this->apiConfig['url'] . "tag/$tag/matcher/$tag_matcher";

        if(!$this->http_delete($url,$results,$errMsg))
        {
            echo($errMsg);
            return false;
        }
        else
        {
            return true;
        }
    }
    
}

?>