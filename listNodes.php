<?php
require 'class.razor.php';

$razor = new razor();


// Get Nodes
$razor->getNodes();

// Get Tag by Tag
//$args = array(
//    'name' => 'amCloud-vlan800',
//    'tag' => 'amCloud-vlan800',
//);
//
//print_r($razor->getTagByTag("amCloud-vlan800"));


// Set a tag
//if($res = $razor->setTag($args))
//{
//    print_r($res);
//}
//

// Get a tag by uuid
//print_r($razor->getTag('7O65IYTwOBSvEtRSXMf7Qe'));


$args = array(
    'tag_rule_uuid' => '7O65IYTwOBSvEtRSXMf7Qe',
    'key' => 'virtual',
    'compare' => 'equal',
    'value' => 'vmware',
    'invert' => "false",
);

if($res = $razor->setTagMatcher($args))
{
  print_r($res);  
}

?>