<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\Collect as CollectModel;
use app\admin\model\Movies;

/**
 * 收藏接口
 */
class Collect extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = [];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 收藏列表
     *
     * @ApiTitle    (收藏列表)
     * @ApiSummary  (收藏列表)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/collect/list)
     * @ApiParams   (name="page", type="integer", required=false, description="页码")
     * @ApiParams   (name="limit", type="integer", required=false, description="显示条数")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    'code':'1',
    'msg':'返回成功'
    })
     */
    public function list()
    {
        $limit = $this->request->get("limit/d", 20);
        $page = $this->request->get("page/d", 1);
        $collectModel = new CollectModel;
        $params = [];
        $params["user_id"] = $this->auth->id;
        $list = $collectModel->getCollectList($params, $page, $limit);

        $this->success('返回成功', $list);
    }

    /**
     * 添加取消收藏
     *
     * @ApiTitle    (添加取消收藏)
     * @ApiSummary  (添加取消收藏)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/collect/addcollect)
     * @ApiParams   (name="movie_id", type="integer", required=true, description="影视ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    'code':'1',
    'msg':'返回成功'
    })
     */
    public function addcollect()
    {
        $movieId = $this->request->post("movie_id/d", 0);

        if (empty($movieId)) {
            $this->error('非法参数');
        }
        //判断movieID是否合法
        $movieModel = new Movies;
        $movieInfo = $movieModel->getMovieInfo($movieId);
        if (empty($movieInfo)) {
            $this->error('非法影视内容');
        }

        //限制点击频率,5秒只能点击一次
        $cacheKey = "collect_{$movieId}_{$this->auth->id}";
        if (cache($cacheKey)) {
            $this->error('操作频繁，请稍后再试');
        }
        cache($cacheKey, 1, 5);

        $params = [
            "movie_id" => $movieId,
            "user_id" => $this->auth->id,
        ];
        $collectModel = new CollectModel;
        //查询当前收藏状态
        $collect = $collectModel->getUserCollect($params);
        $msg = "添加";
        if ($collect) {
            //取消收藏
            $msg = "取消";
            $collectModel->delUserCollect($params);
        } else {
            //添加收藏
            $collectModel->addUserCollect($params);
        }

        $this->success("{$msg}收藏成功");
    }

}
