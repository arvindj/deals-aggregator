<?php
class deals extends Controller
{
    function deals()
    {
        parent::Controller();
        $this->load->model("deals_model");
        $this->load->model("user_model");
    }

    function index()
    {
        $city = $this->_get_city();
        redirect("city/$city");
    }

    function city($city_code='')
    {
        $this->user_model->set_city_in_cookie($city_code);
        $deals = $this->deals_model->get_active_deals_for_city($city_code);
        $varr['deals'] = $deals;
        $varr['city_code'] = $city_code;
        $varr['page_title'] = ucfirst("$city_code deals");
        $this->template->load("template", "deal_list", $varr);
    }

    function deal($deal_id)
    {
        $deal = $this->deals_model->get_deal_info($deal_id);
        $varr['deal'] = $deal[0];
        $varr['page_title'] = $deal[0]->title;
        $varr['city_code'] = $deal[0]->city_code;
        $varr['iframe_page'] = true; 
        $this->template->load("template", "deal",$varr);
    }

    function _get_city()
    {
 /*
 $host_ip = (isset($_SERVER['SERVER_ADDR']))?"":$_SERVER['SERVER_ADDR'];
 $url = "http://api.hostip.info/?ip=".$host_ip;
 $host_details = xml2array(file_get_contents($url));
 $city = isset($host_details['HostipLookupResultSet']['gml:featureMember']['Hostip']['gml:name'])?
              $host_details['HostipLookupResultSet']['gml:featureMember']['Hostip']['gml:name'] : "";
  */
        $city = $this->user_model->get_city_from_cookie();
        if($city == "")
        {
            $city = "bangalore";
        }
        return $city;
    }
}
?>
