<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class sites {
        var $site_data = array();
         
        function _get($site_code)
        {
         if(isset($this->site_data[$site_code])) return;
          $config_xml = APPPATH."/config/deals/sites/$site_code.xml";
          $xml = file_get_contents($config_xml);
          $xml_arr = xml2array($xml);
          $this->site_data[$site_code] = $xml_arr;
         } 

        function get_logo($site_code)
        {
         $this->_get($site_code);
         return $this->site_data[$site_code]['site']['logo_url']; 
        }   
}
?>
