<?php

namespace app\admin\model;

use think\Model;


class Playrecord extends Model
{
    // 表名
    protected $name = 'playrecord';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [
    ];

    public function getPlayList($params, $page = 1, $limit = 20)
    {
        $where = [
            "user_id" => $params["user_id"]
        ];
        $total = self::alias('play')
            ->join('fa_movies mo','mo.id = play.movie_id')
            ->where($where)
            ->count();
        $list = self::alias('play')
            ->join('fa_movies mo','mo.id = play.movie_id')
            ->where($where)
            ->order("play.updatetime", "desc")
            ->page($page, $limit)
            ->select();


        $result = array("total" => $total, "list" => $list);

        return $result;
    }

    public function getUserPlay($params)
    {
        $where = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        $info = self::where($where)->find();

        return $info;
    }

    public function addUserPlay($params)
    {
        $data = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        self::save($data);
        $id = self::getLastInsID();

        return $id;
    }

    public function updateUserPlay($params)
    {
        $where = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];
        $data = [
        ];

        return self::where($where)->update($data);
    }

    public function delUserPlay($params)
    {
        $where = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        return self::where($where)->delete();
    }


}
