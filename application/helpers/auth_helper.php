<?php
defined('BASEPATH') OR exit('No direct script access allowed');

function is_logged_in() {
    $CI =& get_instance();
    $CI->load->library('session');
    return $CI->session->userdata('logged_in') ? true : false;
}

function require_login() {
    if (!is_logged_in()) {
        redirect('auth');
        exit;
    }
}

function is_admin() {
    $CI =& get_instance();
    $CI->load->library('session');
    return strtolower((string)$CI->session->userdata('role')) === 'admin';
}

function require_admin() {
    if (!is_admin()) {
        redirect('home');
        exit;
    }
}
