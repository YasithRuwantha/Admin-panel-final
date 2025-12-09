<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Auth extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        $this->load->model('User_model');
        $this->load->library('session');
        $this->load->helper(['form', 'url', 'cookie']);
    }

    public function index() {
        // Auto-login using remember me token
        if (!$this->session->userdata('logged_in')) {

            $remember_token = get_cookie('remember_token');

            if ($remember_token) {
                $user = $this->User_model->get_user_by_token($remember_token);
                if ($user) {
                    $this->session->set_userdata([
                        'logged_in' => true,
                        'username' => $user['username'],
                        'role' => $user['role']
                    ]);
                    redirect('home');
                }
            }
        }

        if ($this->session->userdata('logged_in')) {
            redirect('home');
        }

        $this->load->view('login');
    }

    public function login() {
        $username = $this->input->post('username');
        $password = $this->input->post('password');
        $remember = $this->input->post('remember'); // checkbox

        $user = $this->User_model->get_user($username);

        if ($user && password_verify($password, $user['password'])) {

            // set session
            $this->session->set_userdata([
                'logged_in' => true,
                'username' => $user['username'],
                'role' => $user['role']
            ]);

            // Remember Me logic
            if ($remember) {
                $token = bin2hex(random_bytes(32)); // 64-char token
                $this->User_model->update_remember_token($user['id'], $token);

                // set cookie for 1 week
                set_cookie([
                    'name' => 'remember_token',
                    'value' => $token,
                    'expire' => 60 * 60 * 24 * 7, // 1 week
                    'secure' => false, // true for HTTPS
                    'httponly' => true
                ]);
            }

            redirect('home');
        } else {
            $data['error'] = 'Invalid username or password';
            $this->load->view('login', $data);
        }
    }

    public function logout() {

        // delete cookie
        delete_cookie('remember_token');

        // remove DB token
        $user = $this->session->userdata('username');
        if ($user) {
            $this->User_model->clear_remember_token($user);
        }

        // destroy session
        $this->session->sess_destroy();

        redirect('auth');
    }
}
