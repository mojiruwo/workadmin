<?php

namespace app\admin\model;

use think\Model;


class Recommend extends Model
{
    // 表名
    protected $name = 'recommend';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public function getRecommendList($params, $page = 1, $limit = 20)
    {

        $where = [];

        $total = self::where($where)
            ->count();
        $list = self::where($where)
            ->order("id", "desc")
            ->page($page, $limit)
            ->select();


        $result = array("total" => $total, "list" => $list);

        return $result;
    }


}
