<?php
function get_site_logo($site_code)
{
  $ci = &get_instance();  
  return $ci->sites->get_logo($site_code);
}

function get_city_list()
{
  $config_xml = APPPATH."/config/deals/cities.xml";
  $xml = file_get_contents($config_xml);
  $xml_arr = xml2array($xml);
  return $xml_arr['cities']['city'];
}
?>
