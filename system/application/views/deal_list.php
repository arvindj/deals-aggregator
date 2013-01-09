<div class='leftCont'>
<script src='<?=config_item("JS_PATH")?>countdown.js'></script>
<?
foreach($deals as $deal)
{
    $more_details_url = $deal->deal_page_url(); 
?>
    <div class='deal_item'>
        <div class='dealtitle'><a href='<?=$deal->deal_page_url()?>'><?=$deal->title?></a></div>
        <table>
        <td>
        <a href='<?=$deal->deal_page_url()?>'><img class='left_thumb' src='<?=$deal->image?>'></a>
        </td>
        <td style='width:300px;'>
        <div class='youpay'>
        You pay: <span class='dealamt'>Rs <?=$deal->sold_price?></span><br/>
        </div>
        <div>
        <div class='pricedet_item'>
        <div class='pricedet_head'>value</div>
        <div class='pricedet_val'>Rs <?=$deal->actual_price?> </div>
        </div>
        <div class='pricedet_item'>
        <div class='pricedet_head'>discount %</div>
        <div class='pricedet_val'><?=$deal->discount_percentage?></div>
        </div>
        <div class='pricedet_item'>
        <div class='pricedet_head'> savings</div>
        <div class='pricedet_val'>Rs <?=$deal->savings()?></div>
        </div>
        </div>
        <div style='height:10px;clear:both'></div>
        Offer closes in: <span class='timer' id='timer_<?=$deal->id?>'></span>
        </td>
        <td style='width:300px;'>
        <div style='font-weight:bold;'><a title="<?=addslashes($deal->title)?>" href='<?=$deal->deal_page_url()?>'>MORE DETAILS</a></div>
        <div style='clear:both;height:50px;'></div>
        <div style='float:right;'><img style='height:30px;' src='<?=get_site_logo($deal->site_code)?>'></img></div>
        </td>
        </table>
        </div>
        <? }?>
        <script>

<?php
    foreach($deals as $deal){
        $i = $deal->id;
?>

        var cd<?=$i?> = new countdown('cd<?=$i?>');
        cd<?=$i?>.Div     = "timer_<?=$i?>";
        cd<?=$i?>.TargetDate    = "<?=date("m/d/Y H:i:s",strtotime($deal->expiry_time))?>";
        cd<?=$i?>.DisplayFormat = "%%D%%<span class='timedesc'>d</span>  %%H%%<span class='timedesc'>h</span>  %%M%%<span class='timedesc'>m</span> %%S%%<span class='timedesc'>s</span>";
        cd<?=$i?>.Setup();
        <?  }  ?>
        </script>
</div><!--ends leftCont-->
<div class='rightCont'>
<?php include("fb_like_box.php");?>
<?php include("affiliates/koovs.php");?>
</div>
