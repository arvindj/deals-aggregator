<?php
function p($t)
{
    print "<pre>";
    print_r($t);
}
function gmt_to_local_ts($ts)
{
    return gmt_to_local($ts, "UP45", TRUE);
}
?>
