<?php
require 'class.razor.php';

$razor = new razor();


// Get Nodes
//$razor->get_nodes();

// Get Tag by Tag
$args = array(
    'name' => 'amCloud-vlan800',
    'tag' => 'amCloud-vlan800',
);
$uuid = $razor->get_tagUUID_by_tag("amCloud-vlan800");

if(!$uuid)
{
  // If no tag exists, add it
  echo("Creating tag" . PHP_EOL);

  // Add a tag
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

if(!$tm)
{
  if($res = $razor->add_tag_matcher($args))
  {
    print_r($res);  
  }
}
else
{
  print_r($tm);
  print_r($razor->delete_tag_matcher($uuid,$tm['@uuid']));
}
?>