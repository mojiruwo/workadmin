<?php

namespace app\admin\model;

use think\Model;


class Movies extends Model
{
    // 表名
    protected $name = 'movies';

    // 自动写入时间戳字段
    protected $autoWriteTimestamp = 'int';

    // 定义时间戳字段名
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    protected $deleteTime = false;

    // 追加属性
    protected $append = [

    ];

    public $categoryArr = [
        "movies" => [
            "type" => 1,
            "title" => "影视",
        ],//影视
        "tv" => [
            "type" => 2,
            "title" => "电视剧",
        ],//电视剧
        "classical" => [
            "type" => 3,
            "title" => "经典剧",
        ],//经典剧
        "other" => [
            "type" => 4,
            "title" => "其他",
        ],//其他
        "hot_classical" => [
            "type" => 5,
            "title" => "热门经典",
        ],//热门经典
        "must_see" => [
            "type" => 6,
            "title" => "必看经典",
        ],//必看经典
        "choose" => [
            "type" => 7,
            "title" => "精挑细选",
        ],//精挑细选
        "recommend" => [
            "type" => 8,
            "title" => "为你推荐",
        ],//为你推荐
    ];

    public function getCategoryList()
    {
        $list = [];
        foreach ($this->categoryArr as $value) {
            $list[] = [
                "type" => $value['type'],
                "title" => $value['title'],
            ];
        }

        return $list;
    }

    public function getMovieList($params, $page = 1, $limit = 20)
    {

        $where = [];

        if (!empty($params['category_id'])) {
            $where['category_id'] = $params['category_id'];
        }

        if (!empty($params['search'])) {
            $where['title'] = ['like', "%{$params['search']}%"];
        }

        $total = self::where($where)
            ->count();
        $list = self::where($where)
            ->order("id", "desc")
            ->page($page, $limit)
            ->select();


        $result = array("total" => $total, "list" => $list);

        return $result;
    }

    public function getMovieInfo($id)
    {
        $where = ["id" => $id];

        $info = self::where($where)->find();

        return $info;
    }


}
