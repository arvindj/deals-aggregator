<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?=$page_title?> | Lootinlot.com</title>
<meta name='keywords' content='best deals, discounts, coupons, restaurant deals, spa deals, bangalore, delhi, chennai, mumbai, pune'></meta>
<link href="<?=config_item('CSS_PATH')?>main.css" rel="stylesheet" type="text/css"/>
<meta name="google-site-verification" content="goCctRseaZnMhwIIZAzKMdZ23dK01CY4GES2dBwshkM" />
</head>
<body>
<div id="container">
    <div id="header" <?php if(isset($iframe_page) and $iframe_page==1) echo "style='height:90px;'"?>>
    	<div id="logo">
        <a href="/"><span class="orange">Loot</span>in<span class='orange'>Lot</span></a>
        <span style='margin-left:20px;color:#ddd;font-size:15px;'>@<span style='margin-left:10px;'><?=ucfirst($city_code)?></span></span>
<?php if(!isset($iframe_page) or $iframe_page==0) {?>
        <span style='float:right;padding-right:200px;color:#eee;'>
          Change your city:
          <select id='city' ONCHANGE="location = this.options[this.selectedIndex].value;">
          <?php
           
            $city_list = get_city_list();
            foreach($city_list as $c) { ?>
          
            <option value='<?=$c['code']?>' <?=(($city_code==$c["code"])?"selected='true'": "")?>><?=$c['name']?></option>
            <?php }?>
          </select> 
        </span>
<?}?>
      </div>
<?php if(!isset($iframe_page) or $iframe_page==0) {?>
        <div id="menu">
        	<ul>
            <li><a href="#" class="active">CURRENT DEALS</a></li>
            <!--<li><a href="#">about us</a></li> -->
            </ul>
        </div>
<?}?>
    </div>
    <!--end header -->
    <!-- main -->
<?php if(!isset($iframe_page) or $iframe_page==0) {?>
    <div id="main">
      <div id='content'>
<?}?>
      <?=$contents?>
<?php if(!isset($iframe_page) or $iframe_page==0) {?> 
     </div>
    </div>
<?}?>
    <!-- end main -->
    <!-- footer -->
    <div id="footer">
    <div id="left_footer">
    </div> 
    <div class='footer_links'>
<?php
                foreach(get_city_list() as $c)
                {
?>
<div class='link_item'>  
  <a title="Best deals, discounts in <?=$c['name']?>" href='/city/<?=$c['code']?>'>Deals in <?=$c['name']?></a>
</div>
<?
                }
?> 
    </div>
    <div id="right_footer">
    </div>
    </div>
    <!-- end footer -->
</div>
<?php
            if(getenv("env") == 'prod')           
                include("google_analytics.php");
?>
</body>
</html>
