<?php
include_once("DataObjects/dealObj.php");
class deals_model extends Model
{
    function deals_model()
    {
        parent::Model();
        $this->table = "deals";
    }

    function get_deals_by_src_uniq_id($src_uniq_id, $site_code)
    {
        return $this->dbi->_select($this->table, "*", Array('src_uniq_id'=>$src_uniq_id, 'site_code'=>$site_code), "expiry_time desc");
    }

    function add_item($details)
    {
        $details['added_time'] = date("Y-m-d H:i:s");
        $this->dbi->_insert($this->table, $details);
    }

    function get_active_deals_for_city($city_code)
    {
        $result = $this->dbi->_select($this->table, "*", Array('city_code'=>$city_code, 'valid'=>1, 'expiry_time >' => date("Y-m-d H:i:s")));
        return DealObj::arr2obj($result);
    }

    function get_deal_info($deal_id)
    {
        $result =  $this->dbi->_select($this->table, "*", Array('id'=>$deal_id));
        return DealObj::arr2obj($result);
    }
}
?>
