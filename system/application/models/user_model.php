<?php
class user_model extends Model
{

function user_model()
{
parent::Model();
}

function set_city_in_cookie($city)
{
  set_cookie("city",$city,0);
}

function get_city_from_cookie()
{
  return get_cookie("city");
}

}
?>
