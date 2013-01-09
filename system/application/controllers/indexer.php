<?php

class Indexer extends Controller {

    var $site_code;
    var $city;
    var $site_url;

    function Indexer()
    {
        parent::Controller();	
        $this->load->library("simple_html_dom");    
        $this->load->model("deals_model");
        $this->load->helper("xml_helper");
        // $this->output->enable_profiler(TRUE);
    }

    function index($sitecode='', $citycode='')
    {
        if($sitecode=='') return; 
        $config_xml = APPPATH."/config/deals/sites/$sitecode.xml";
        $xml = file_get_contents($config_xml);
        $xml_arr = xml2array($xml);
        $cities_arr = $xml_arr['site']['cities'];
        $this->site_code = $xml_arr['site']['site_code'];
        print "\n\n$this->site_code:";
        $this->site_url = $xml_arr['site']['site_url'];
        $crawl_base_url = $xml_arr['site']['crawl_base_url'];
        foreach($cities_arr['entry'] as $city_det)
        {
            $this->city=$city_det['city'];
            if($citycode != "" && $citycode!=$this->city)
                continue;
            $uris = $city_det['uri'];
            if(count($uris)==1 and !is_array($uris))
            {
                $uris = Array($uris);
            }
            foreach($uris as $uri)
            {
                $url = $crawl_base_url.$uri;
                print "\n$url ";
                $this->$sitecode($url);
            }
        }
    } 

    function snapdeal($url)
    {
        $html = file_get_html($url);
        $contentArr = $html->find("div[class=content-placeholder]");
        foreach($contentArr as $content)
        {  
            $title_node = $content->find("h3[class=deal-title]",0);
            $title = preg_replace("/<span(.*)span>/", "",trim(str_replace("\n","",$title_node->innertext)));
            
            $advance_amount_node = $content->find("div[class=rs]",0);
            $advance_amount  =preg_replace("/<span(.*)span>/", "",$advance_amount_node->innertext);
            
            $price_details_node = $content->find("div[class=gcontarea-right-valuetxt]",0);
            if(count($price_details_node) == 0) continue;
            $price_details_node = $price_details_node->find("span[class=valuetxt-right]");
            $actual_price = trim(preg_replace("/<span(.*)span>/", "",$price_details_node[0]->innertext));

            $discount_percentage = trim(preg_replace("/[(<span(.*)span>)]/", "",$price_details_node[1]->innertext));
            $discount_value = preg_replace("/<span(.*)span>/", "",$price_details_node[2]->innertext);

            $time_rem = $content->find("div[class=timer]",0)->innertext;//in milli seconds
            $expiry_time = date("Y-m-d H:i:00", time()+floor($time_rem/1000));

            $buy_link = $content->find("div[class=gcontarea-right-content]",0)->find("a[href]",0)->getAttribute("href");

            $click_url = str_replace("/buy","",$buy_link);//TODO, give correct link 
            $thumbnail_image = $content->find("div[class=slider-wrap]",0)->find("img",0)->getAttribute("src");

            $url_comp = parse_url($click_url); 
            $src_uniq_id_arr = explode("-",trim($url_comp['path'],'/'));    
            $src_uniq_id = implode(array_slice($src_uniq_id_arr,2),"-");

            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $actual_price - $discount_value; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";
            $this->check_and_add_deal($db_entry);
        }
    }



    function mydala($url='')
    {
        $html = file_get_html($url);	
        $contentArr = $html->find("div[id=dealbody]");
        foreach($contentArr as $content)
        {     
            $title_node = $content->find("div[id=dealname] h1",0);
            $title = trim($title_node->innertext);

            $price_details_node = $content->find("div[id=dealContent] ul li",1)->find("div");
            $actual_price = trim(preg_replace("/<span(.*)span>/", "",$price_details_node[1]->innertext));

            $discount_percentage = trim(preg_replace("/[(<span(.*)span>)]/", "",$price_details_node[2]->innertext));
            $discount_value = preg_replace("/<span(.*)span>/", "",$price_details_node[3]->innertext);

            if($content->find("div[id=rightArea] a[id=buydeal]") == false)
                continue;
            $buy_link = $content->find("div[id=rightArea] a[id=buydeal]",0)->getAttribute("href");
            $thumbnail_image = $content->find("div[id=DealDetails] img",0)->getAttribute("src");


            $url_comp = parse_url($buy_link); 
            parse_str($url_comp['query']);
            $src_uniq_id = $eventid;
            $click_url_arr = parse_url($html->find("div[id=Sh] ul li",1)->find("div map area",0)->getAttribute("href"));
            parse_str($click_url_arr['query']);
            $click_url = $u;

            preg_match("/countdown\((.*)\)/", $html, $matches);
            $expiry_time = date("Y-m-d H:i:00",strtotime(implode("-",array_slice(explode(",",$matches[1]),0,3)))-12*60*60);

            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $actual_price - $discount_value; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";       
            $this->check_and_add_deal($db_entry);  
        }
    }

    function koovs($url='')
    {
        try{
            $html = file_get_html($url);	
            $contentArr = $html->find("div[class=banner-one]");
            $expiryTimeArr = $html->find("div[class=all-banner] script");
            $index = 0; 
            foreach($contentArr as $content)
            {  
                $title_node = $content->find("div[class=under-text]",0);
                $title = trim($title_node->plaintext);
                
                $price_details_node = $content->find("div[class=price_repeat_bottom_1]",0)->find("div[class=big]");
                
                $index = 0;
                if(count($price_details_node) > 3)
                    $index = 1; 
                $actual_price = $this->strip_to_numbers_only(preg_replace("/<span(.*)span>/", "",$price_details_node[$index]->innertext));
                $discount_percentage = $this->strip_to_numbers_only(preg_replace("/[(<span(.*)span>)]/", "",$price_details_node[$index+1]->innertext));
                $sold_value = $this->strip_to_numbers_only(preg_replace("/<span(.*)span>/", "",$price_details_node[$index+2]->innertext));

                $buy_link_node = $content->find("a[class=buynow]",0); 
                if(!isset($buy_link_node->href)) continue; // to check if the deal is expired or not 
                $buy_link = $buy_link_node->href;


                preg_match("/txn\/confirm\/(.*)\//", $buy_link, $matches);
                $src_uniq_id = $matches[1];
                $click_url = $this->site_url."deals/deal/".$src_uniq_id;

                $thumbnail_image = $content->find("div[class=banner-one-image]",0)->find("img",0)->getAttribute("src");

                
                preg_match("/endDate = \"(.*)\";/", $expiryTimeArr[$index++]->innertext, $matches);
                $expiry_time = date("Y-m-d H:i:00",strtotime($matches[1]));

                $credit = "";  
                $db_entry = array();  
                $db_entry['title'] = $title;
                $db_entry['site_code'] = $this->site_code;
                $db_entry['src_uniq_id'] = $src_uniq_id;
                $db_entry['title'] = $title; 
                $db_entry['description'] = ""; 
                $db_entry['image'] = $thumbnail_image; 
                $db_entry['actual_price'] = $actual_price; 
                $db_entry['sold_price'] = $sold_value; 
                $db_entry['discount_percentage'] = $discount_percentage; 
                $db_entry['category'] = $this->get_category($title); 
                $db_entry['expiry_time'] = $expiry_time; 
                $db_entry['click_url'] = $click_url; 
                $db_entry['credit'] = $credit; 
                $db_entry['deal_type'] = "voucher"; 
                $db_entry['city_code'] = $this->city; 
                $db_entry['sub_locations'] = "";
                 $this->check_and_add_deal($db_entry);    

            }
        }
        catch(Exception $e)
        {
            // var_dump($e->getMessage());
            print "error";
        }
    }

    function scoopstr($url='')
    {
        $html = file_get_html($url);	
        $contentArr = $html->find("div[id=content]");
        foreach($contentArr as $content)
        {     
            $title_node = $content->find("div[class=width_full] h1",0);
            $title = trim($title_node->plaintext);

            $price_details_node = $content->find("div[class=value] dl dd");
            $actual_price = $this->strip_to_numbers_only($price_details_node[0]->innertext);

            $discount_percentage = $this->strip_to_numbers_only($price_details_node[1]->innertext);
            $discount_value = $this->strip_to_numbers_only($price_details_node[2]->innertext);

            $click_url = $content->find("div[class=width_full] h1 a",0)->getAttribute("href");

            $thumbnail_image = $content->find("div[class=image_reel] img",0)->getAttribute("src");

            preg_match("{/deals/([0-9]*)-}", $click_url, $matches);
            $src_uniq_id = $matches[1];

            preg_match("/endDate = \"(.*)\";/", $html, $matches);
            $expiry_time = date("Y-m-d H:i:00",strtotime($matches[1]));


            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $actual_price - $discount_value; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";       

            $this->check_and_add_deal($db_entry);  
        }
    }

    function taggle($url='')
    {
        $html = file_get_html($url);	
        $contentArr = $html->find("div[class=offersBox] div[class=offer]");
        foreach($contentArr as $content)
        {     
            $title_node = $content->find("h1 a",0);
            $title = trim($title_node->plaintext);
            
            $price_details_node = $content->find("div[class=buyBox]", 0);
            $actual_price = $this->strip_to_valid_amount($price_details_node->find("span[class=actualAmt]",0)->plaintext);

            $discount_percentage = $this->strip_to_valid_amount($price_details_node->find("span[class=discount] h2",0)->plaintext);
            $sold_price = $this->strip_to_valid_amount($price_details_node->find("span[class=amount] h2",0)->plaintext);
            
            $click_url = $this->site_url.$title_node->href;

            $thumbnail_image = $this->site_url.$content->find("div[class=offerImage] img",0)->src;

            preg_match("{/offer/([a-zA-Z0-9\-]*)}", $click_url, $matches);
            $src_uniq_id = $matches[1];
            
            $expiry_time_node  = $content->find("div[class=offer-clock]",0); 
            $hour = $expiry_time_node->find("b[class=hour]",0)->plaintext;
            $minute = $expiry_time_node->find("b[class=min]",0)->plaintext;
            $seconds = $expiry_time_node->find("b[class=sec]",0)->plaintext;
            $remaining_time_stamp = $hour*60*60 + $minute*60 + $seconds;
            $expiry_time = date("Y-m-d H:i:00",time()+$remaining_time_stamp);

            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $sold_price; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";      
            $this->check_and_add_deal($db_entry);  
        }
    }

    function dealivore($url)
    {
        $html = file_get_html($url);	
        $contentArr = $html->find("div[class=content-left]");
        foreach($contentArr as $content)
        {     
            $title_node = $content->find("div[class=title-p]",0);
            $title = trim($title_node->plaintext);
            $price_details_node = $content->find("div[class=pricing-details]", 0);
            $price_details_node = $price_details_node->find("td[class=text1]");
            $actual_price = $this->strip_to_valid_amount($price_details_node[0]->plaintext);

            $discount_percentage = $this->strip_to_valid_amount($price_details_node[1]->plaintext);
            $discount_price = $this->strip_to_valid_amount($price_details_node[2]->plaintext);
            $sold_price = $actual_price - $discount_price;

            $buy_link = $content->find("a",0)->href; 
            $click_url = $url;//should be changed later

            $thumbnail_image = $content->find("div[class=inn-call-ac-desc]",0)->find("img",0)->src;

            preg_match("{/index/([0-9]*)}", $buy_link, $matches);
            $src_uniq_id = $matches[1];
            
            $expiry_time_node  = $content->find("div[class=time-p]",0); 
            $hour = $expiry_time_node->find("span[id=timeleft_hours]",0)->plaintext;
            $minute = $expiry_time_node->find("span[id=timeleft_minutes]",0)->plaintext;
            $seconds = $expiry_time_node->find("span[id=timeleft_seconds]",0)->plaintext;
            $remaining_time_stamp = $hour*60*60 + $minute*60 + $seconds;
            $expiry_time = date("Y-m-d H:i:00",time()+$remaining_time_stamp);

            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $sold_price; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";      
            $this->check_and_add_deal($db_entry);  
        }
    }
   
    function dealsandyou($url)
    {
        $html = file_get_html($url);	
        $contentArr = $html->find("td[width=730]");
        foreach($contentArr as $content)
        {     
            $title_node = $content->find("div[class=top_big_text_div]",0);
            $title = trim($title_node->plaintext);
           
            $price_details_node = $content->find("tr[id=codStatus]", 0)->find("table",0)->find("tr",1)->find("td span");
            
            $actual_price = $this->strip_to_valid_amount($price_details_node[0]->plaintext);
            $discount_percentage = $this->strip_to_valid_amount($price_details_node[1]->plaintext);
            $discount_price = $this->strip_to_valid_amount($price_details_node[2]->plaintext);
            $sold_price = $actual_price - $discount_price;

            $buy_link = $content->find("a",0)->href; 
        
            $click_url = str_replace("&action=buy_now", "", $buy_link);//should be changed later

            $thumbnail_image = "";
            $thumbnail_image_node = $content->find("td[class=roudn_box_bg_2]",0)->find("img[src]");
            foreach( $thumbnail_image_node as $t)
             if($t->width>400)
                $thumbnail_image = $t->src; 
            
            preg_match("{products_id=([0-9]*)}", $buy_link, $matches);
            $src_uniq_id = $matches[1];
            
            $expiry_time_src  = $content->find("td[class=voilet_box]",0)->find("script[src]",0)->src; 
            preg_match("{countto=([0-9\-\ \:]*)}", $expiry_time_src, $matches);
            $expiry_time = date("Y-m-d H:i:00",strtotime($matches[1]));

            $db_entry = array();  
            $db_entry['title'] = $title;
            $db_entry['site_code'] = $this->site_code;
            $db_entry['src_uniq_id'] = $src_uniq_id;
            $db_entry['title'] = $title; 
            $db_entry['description'] = ""; 
            $db_entry['image'] = $thumbnail_image; 
            $db_entry['actual_price'] = $actual_price; 
            $db_entry['sold_price'] = $sold_price; 
            $db_entry['discount_percentage'] = $discount_percentage; 
            $db_entry['category'] = $this->get_category($title); 
            $db_entry['expiry_time'] = $expiry_time; 
            $db_entry['click_url'] = $click_url; 
            $db_entry['credit'] = ""; 
            $db_entry['deal_type'] = "voucher"; 
            $db_entry['city_code'] = $this->city; 
            $db_entry['sub_locations'] = "";      
            $this->check_and_add_deal($db_entry);  
        }
    }
    function check_and_add_deal($db_entry)
    {
        print " : crawled";
        if($this->should_deal_be_added($db_entry))
        {   
            $this->deals_model->add_item($db_entry);
            print " : added";
        }
    }

    function should_deal_be_added($db_entry)
    {
        $deals = $this->deals_model->get_deals_by_src_uniq_id($db_entry['src_uniq_id'], $db_entry['site_code']); 
        if($deals && count($deals)>0)
        {
            foreach($deals as $deal)
                if(($deal["city_code"] == $db_entry["city_code"]))
                {
                    if(($deal['expiry_time'] >= $db_entry["expiry_time"]) and ((strtotime($deal['expiry_time']) - strtotime($db_entry["expiry_time"])) < (60*60)))
                    {
                        return false;
                    }
                }
            return true;
        }
        return !$deals; 
    }

    function strip_to_numbers_only($string)
    {
        return preg_replace("/[^0-9]/","",$string);
    }

    //this includes the product range
    function strip_to_valid_amount($string)
    {
        return preg_replace("/[^0-9\-]/","",$string);
    }

    function get_category($text)
    {
        $categories = Array(
            "spa"=> Array("keywords"=> Array("spa","hair","haircut","salon","beauty","massage")),
            "food"=>Array("keywords"=>Array("food","lunch","buffet","restaurant","dishes","cuisine","taste", "beer", "meal", "lounge","veg","chocolates","delicious")),
            "electronics"=>Array("keywords"=>Array("electronics","mobiles","mobile","tv", "player", "usb")),
            "games"=>Array("keywords"=>Array("game","gaming","play")),
            "travel" => Array("keyowrds" => Array("holiday", "travel"))
        );

        $words = explode(" ",str_replace(","," ", strtolower($text)));
        foreach($categories as $k=>$c )
        {
            if(count(array_intersect($words, $c['keywords']))>0)
                return $k;
        }
        return "";
    }
}
