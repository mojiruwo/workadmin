<?php

namespace app\admin\model;

use think\Model;


class Comment extends Model
{
    // 表名
    protected $name = 'movie_comment';
    
    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function addUserComment($params)
    {
        $data = [
            "movie_id" => $params["movie_id"],
            "user_id" => $params["user_id"],
            "content" => $params["content"],
        ];

        self::save($data);
        $id = self::getLastInsID();

        return $id;
    }
    

    







}
