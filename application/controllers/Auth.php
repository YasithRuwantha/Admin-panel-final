<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {
    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper(array('form', 'url'));
    }

    public function index() {
        if ($this->session->userdata('logged_in')) {
            redirect('home');
        }
        $this->load->view('login');
    }

    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $user = $this->User_model->get_user($username);
        if ($user && password_verify($password, $user['password'])) {
            $this->session->set_userdata(['logged_in' => true, 'username' => $user['username'], 'role' => $user['role']]);
            redirect('home');
        } else {
            $data['error'] = 'Invalid username or password';
            $this->load->view('login', $data);
        }
    }

    public function logout() {
        $this->session->sess_destroy();
        redirect('auth');
    }
}
