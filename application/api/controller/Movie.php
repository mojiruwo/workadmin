<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\Movies;
use app\admin\model\Comment as CommentModel;

/**
 * 影视接口
 */
class Movie extends Api
{

    //如果$noNeedLogin为空表示所有接口都需要登录才能请求
    //如果$noNeedRight为空表示所有接口都需要验证权限才能请求
    //如果接口已经设置无需登录,那也就无需鉴权了
    //
    // 无需登录的接口,*表示全部
    protected $noNeedLogin = ['*'];
    // 无需鉴权的接口,*表示全部
    protected $noNeedRight = ['*'];

    public function _initialize()
    {
        parent::_initialize();
    }

    /**
     * 影视列表
     *
     * @ApiTitle    (影视列表)
     * @ApiSummary  (影视分类列表)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/movie/list)
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
        $movieModel = new Movies;
        $returnCategory = [
            'movies',
            'tv',
            'classical',
            'hot_classical',
            'must_see',
        ];
        $list = [];
        foreach ($returnCategory as $value) {
            $categoryId = $movieModel->categoryArr[$value]['type'] ?? 0;
            if (empty($categoryId)) {
                continue;
            }
            $list[$value] = $movieModel->getMovieList(["category_id" => $categoryId], 1, 10)["list"];
        }
        $this->success('返回成功', $list);
    }

    /**
     * 影视详情
     *
     * @ApiTitle    (影视详情)
     * @ApiSummary  (影视详情)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/movie/info)
     * @ApiParams   (name="id", type="integer", required=true, description="影视ID")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    'code':'1',
    'msg':'返回成功'
    })
     */
    public function info()
    {
        $id = $this->request->request("id");
        if (empty($id)) {
            $this->error('无效的ID');
        }
        $movieModel = new Movies;
        $info = $movieModel->getMovieInfo($id);

        $this->success('返回成功', $info);
    }

    /**
     * 添加影评
     *
     * @ApiTitle    (添加影评)
     * @ApiSummary  (添加影评)
     * @ApiMethod   (POST)
     * @ApiRoute    (/api/movie/addcomment)
     * @ApiParams   (name="movie_id", type="integer", required=true, description="影视ID")
     * @ApiParams   (name="comment", type="string", required=true, description="影评内容")
     * @ApiReturnParams   (name="code", type="integer", required=true, sample="0")
     * @ApiReturnParams   (name="msg", type="string", required=true, sample="返回成功")
     * @ApiReturnParams   (name="data", type="object", sample="{'user_id':'int','user_name':'string','profile':{'email':'string','age':'integer'}}", description="扩展数据返回")
     * @ApiReturn   ({
    'code':'1',
    'msg':'返回成功'
    })
     */
    public function addcomment()
    {
        $movieId = $this->request->post("movie_id/d", 0);
        $comment = $this->request->post("comment", "");

        if (empty($movieId) || empty($comment)) {
            $this->error('非法参数');
        }
        //判断movieID是否合法
        $movieModel = new Movies;
        $movieInfo = $movieModel->getMovieInfo($movieId);
        if (empty($movieInfo)) {
            $this->error('非法影视内容');
        }

        //限制点击频率,5秒只能点击一次
        $cacheKey = "comment_{$movieId}_{$this->auth->id}";
        if (cache($cacheKey)) {
            $this->error('操作频繁，请稍后再试');
        }
        cache($cacheKey, 1, 5);

        $params = [
            "movie_id" => $movieId,
            "user_id" => $this->auth->id,
            "content" => $comment,
        ];
        $commentModel = new CommentModel;
        //添加影评
        $commentModel->addUserComment($params);

        $this->success("添加影评成功");
    }

}
