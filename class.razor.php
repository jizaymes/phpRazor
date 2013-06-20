<?php
require 'JSON.php'; // Built in json_decode is BROKEN by razor JSON responses

class razor
{
    
    var $apiConfig = array(
      'url' => "http://localhost:8026/razor/api/",
    );

    /* ------------------------------------------------------------------------------------------------------- */
        
    public function http_get($url, &$results,&$errMsg = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

        $response = curl_exec($ch);

        $json = new Services_JSON();  
        
        $response = json_decode($response,true);

        if($response['http_err_code'] != 200)
        {
                $errMsg = $response['http_err_code'];
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

        $json = new Services_JSON();  
        $response = $json->decode($response,true);
                
        if($response->http_err_code != 200)
        {
                $errMsg = $response->result;
                return false;
        }
        else
        {
                $results = $response->response;
                return true;
        }
    }

    
    
    /* ------------------------------------------------------------------------------------------------------- */
    
    public function getNodes()
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

    public function getTagByTag($tag)
    {
        $results = null;
        $errMsg = null;
        
        $url = $this->apiConfig['url'] . "tag/";
        
        if(!$this->http_get($url,$results,$errMsg))
        {
            return false;
        }
        else
        {
            
            // Normalize the JSON return object
            foreach($results as $tags)
            {
                
                if($tags['@tag'] == $tag )
                {
                    return $tags;
                }
                
            }
        }
    }

    
    /* ------------------------------------------------------------------------------------------------------- */

    public function getTag($tag = "")
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

    public function setTag($args)
    {
        $results = null;
        $errMsg = null;
        
        $url = $this->apiConfig['url'] . "tag";
        
        if(!$this->http_post($url,$args,$results,$errMsg))
        {
            return false;
        }
        else
        {
            return $results;
        }
    }

    /* ------------------------------------------------------------------------------------------------------- */

    public function setTagMatcher($args)
    {
        $results = null;
        $errMsg = null;
        
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


    
}

?>