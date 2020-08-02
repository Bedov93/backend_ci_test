<?php

/**
 * Created by PhpStorm.
 * User: mr.incognito
 * Date: 27.01.2020
 * Time: 10:10
 */
class CommentLikes_model extends CI_Emerald_Model
{
    const CLASS_TABLE = 'comment_likes';

    public $comment_id;

    public function get_id()
    {
        return $this->comment_id;
    }

    public static function get_by_comment_id($comment_id)
    {
        $data = App::get_ci()->s->from(self::CLASS_TABLE)->where(['comment_id' => $comment_id])->one();
        return $data ? (new self())->set($data) : false;
    }

    public static function create(array $data)
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->insert($data)->execute();
    }

    public function delete()
    {
        App::get_ci()->s->from(self::CLASS_TABLE)->where(['comment_id' => $this->get_id()])->delete()->execute();
        return (App::get_ci()->s->get_affected_rows() > 0);
    }

}