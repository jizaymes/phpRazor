<?php
require 'class.razor.php';

$razor = new razor();


// Get Nodes
$nodes = $razor->get_node();



// Get Tag by Tag
$args = array(
    'name' => 'tagname',
    'tag' => 'tag',
);
$uuid = $razor->get_tagUUID_by_tag("tag");

if(!$uuid)
{
  // If no tag exists, add it
  if($res = $razor->add_tag($args))
  {
      $uuid = $res['@uuid'];
  }
}

$tagInfo = $razor->get_tag($uuid);

$args = array(
    'tag_rule_uuid' => $tagInfo['@uuid'],
    'key' => 'virtual',
    'compare' => 'equal',
    'value' => 'vmware',
    'invert' => "false",
);

$tm = $razor->get_tag_matcher($args);

// If tag matcher doesnt exist, create it
if(!$tm)
{
  if($res = $razor->add_tag_matcher($args))
  {
    $tm = $razor->get_tag_matcher($args);
  }
}

if($razor->delete_tag_matcher($uuid,$tm['@uuid']))
{
    $razor->delete_tag($uuid);
    print_r($razor->get_tag($uuid)); // displays nothing
}
?>