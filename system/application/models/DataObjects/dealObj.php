<?php
class DealObj
{
var $id;
var $site_code;
var $src_uniq_id;
var $title;
var $description;
var $image;
var $actual_price;
var $sold_price;
var $discount_percentage;
var $category;
var $valid;
var $expiry_time;
var $added_time;
var $updated_time;
var $click_url;
var $credit;
var $deal_type;
var $city_code;
var $sub_locations;

function deal_page_url()
{
 return base_url().index_page()."deal/".$this->id;  
}

function savings()
{
    if(strpos($this->sold_price, "-"))
    {
        list($s1, $s2) = explode("-", $this->sold_price);
        list($a1, $a2) = explode("-", $this->actual_price);
        return ($a1-$s1)." - ".($a2-$s2);
    }
    else
        return $this->actual_price - $this->sold_price;
}
static function arr2obj($arr)
{
  $arr_of_objects = array();
  if(count($arr) > 0 and $arr)
  {
   foreach($arr as $item)
    {
      $obj = new DealObj(); 
      foreach($item as $k=>$v)
        $obj->$k = $v;
      $arr_of_objects[] = $obj;
    }
  }
  return $arr_of_objects;
}
}
?>
