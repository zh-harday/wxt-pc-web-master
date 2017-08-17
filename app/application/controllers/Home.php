<?php

/**
 * Created by PhpStorm.
 * User: keller
 * Date: 2017/3/9
 * Time: 下午2:53
 */

class Home extends CI_Controller
{
    public function index()
    {
        $this->load->helper('url');
        redirect("v2");
    }



}