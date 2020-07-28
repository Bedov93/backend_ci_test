<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 10.11.2018
 * Time: 21:36
 */
class Main_page extends MY_Controller
{

    public function __construct()
    {
        parent::__construct();

        App::get_ci()->load->model('User_model');
        App::get_ci()->load->model('Login_model');
        App::get_ci()->load->model('Post_model');

        $this->load->library('form_validation');

        if (is_prod()) {
            die('In production it will be hard to debug! Run as development environment!');
        }
    }

    public function index()
    {
        $user = User_model::get_user();


        App::get_ci()->load->view('main_page', ['user' => User_model::preparation($user, 'default')]);
    }

    public function get_all_posts()
    {
        $posts = Post_model::preparation(Post_model::get_all(), 'main_page');
        return $this->response_success(['posts' => $posts]);
    }

    public function get_post($post_id)
    { // or can be $this->input->post('news_id') , but better for GET REQUEST USE THIS

        $post_id = intval($post_id);

        if (empty($post_id)) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
        }

        try {
            $post = new Post_model($post_id);
        } catch (EmeraldModelNoDataException $ex) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
        }


        $posts = Post_model::preparation($post, 'full_info');
        return $this->response_success(['post' => $posts]);
    }


    public function comment()
    { // or can be App::get_ci()->input->post('news_id') , but better for GET REQUEST USE THIS ( tests )


        if (!User_model::is_logged()) {
            return $this->response_error(CI_Core::RESPONSE_GENERIC_NEED_AUTH);
        }

        $rules = array(
            array(
                'field' => 'post_id',
                'label' => 'Post id',
                'rules' => 'trim|required|integer',
            ),
            array(
                'field' => 'message',
                'label' => 'Message',
                'rules' => 'trim|required',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {

            $post_id = App::get_ci()->input->post('post_id', TRUE);
            $message = App::get_ci()->input->post('message', TRUE);

            if (empty($post_id) || empty($message)) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_WRONG_PARAMS);
            }

            try {
                $post = new Post_model($post_id);
            } catch (EmeraldModelNoDataException $ex) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_NO_DATA);
            }

            try {
                $comment = Comment_model::create(array(
                    'user_id' => User_model::get_session_id(),
                    'assign_id' => $post_id,
                    'text' => $message,
                ));

                return $this->response_success(
                    [
                        'comment' =>
                            Comment_model::preparation(
                                [$comment],
                                'full_info'
                            )
                    ]
                );
            } catch (EmeraldModelSaveException $ex) {
                return $this->response_error(CI_Core::RESPONSE_GENERIC_INTERNAL_ERROR);
            }


            $posts = Post_model::preparation($post, 'full_info');
            return $this->response_success(['post' => $posts]);
        } else {
            return $this->response_error("error", $this->form_validation->error_array());
        }
    }


    public function login()
    {
        $rules = array(
            array(
                'field' => 'email',
                'label' => 'Email',
                'rules' => 'trim|required|valid_email',
            ),
            array(
                'field' => 'password',
                'label' => 'Password',
                'rules' => 'trim|required|alpha_numeric',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {

            try {
                Login_model::login($this->input->post());
            } catch (CriticalException $e) {
                return $this->response_error("error", $e->getMessage());
            }

            return $this->response_success(['user' => App::get_ci()->session->id]);
        } else {
            return $this->response_error("error", $this->form_validation->error_array());
        }
    }


    public function logout()
    {
        Login_model::logout();
        redirect(site_url('/'));
    }

    public function add_money()
    {

        $rules = array(
            array(
                'field' => 'sum',
                'label' => 'Sum',
                'rules' => 'trim|required|integer',
            ),
        );

        $this->form_validation->set_rules($rules);

        if ($this->form_validation->run()) {

            $sum = App::get_ci()->input->post('sum');

            return $this->response_success(['amount' => rand(1, 55)]);

        } else {
            return $this->response_error("error", $this->form_validation->error_array());
        }
    }

    public function buy_boosterpack()
    {
        // todo: add money to user logic
        return $this->response_success(['amount' => rand(1, 55)]);
    }


    public function like()
    {
        // todo: add like post\comment logic
        return $this->response_success(['likes' => rand(1, 55)]); // Колво лайков под постом \ комментарием чтобы обновить
    }

}
