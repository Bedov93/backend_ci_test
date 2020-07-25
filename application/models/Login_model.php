<?php
class Login_model extends CI_Model {

    public function __construct()
    {
        $this->load->database();
        parent::__construct();

    }

    public static function logout()
    {
        App::get_ci()->session->unset_userdata('id');
    }



    public static function login($data)
    {

        $user = App::get_ci()->db;
        $user->like('email', $data['email']);
        $user->like('password', $data['password']);
        $user = $user->get('user')->first_row();

        if($user) {
            $user_id = $user->id;
        }

        // если перенедан пользователь
        if (empty($user_id))
        {
            throw new CriticalException('No id provided!');
        }

        App::get_ci()->session->set_userdata('id', $user_id);
    }


}
