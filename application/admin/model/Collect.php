<?php

namespace app\admin\model;

use think\Model;


class Collect extends Model
{
    // 表名
    protected $name = 'collect';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function getCollectList($params, $page = 1, $limit = 20)
    {
        $where = [
            "user_id" => $params["user_id"]
        ];
        $total = self::alias('col')
            ->join('fa_movies mo','mo.id = col.movie_id')
            ->where($where)
            ->count();
        $list = self::alias('col')
            ->join('fa_movies mo','mo.id = col.movie_id')
            ->where($where)
            ->order("col.id", "desc")
            ->page($page, $limit)
            ->select();


        $result = array("total" => $total, "list" => $list);

        return $result;
    }

    public function getUserCollect($params)
    {
        $where = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        $info = self::where($where)->find();

        return $info;
    }

    public function addUserCollect($params)
    {
        $data = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        self::save($data);
        $id = self::getLastInsID();

        return $id;
    }

    public function delUserCollect($params)
    {
        $where = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
        ];

        return self::where($where)->delete();
    }


}
