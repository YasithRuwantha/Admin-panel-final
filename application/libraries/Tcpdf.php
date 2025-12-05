<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// Include the main TCPDF library
require_once('tcpdf/tcpdf.php');

class Tcpdf extends TCPDF
{
    public function __construct()
    {
        parent::__construct();
    }
}
