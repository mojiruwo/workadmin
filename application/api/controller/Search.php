<?php

namespace app\api\controller;

use app\common\controller\Api;
use app\admin\model\Movies;
use app\admin\model\Recommend as RecommendModel;

/**
 * 搜索接口
 */
class Search extends Api
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
     * 搜索列表
     *
     * @ApiTitle    (搜索列表)
     * @ApiSummary  (搜索列表)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/search/list)
     * @ApiParams   (name="page", type="integer", required=false, description="页码")
     * @ApiParams   (name="limit", type="integer", required=false, description="显示条数")
     * @ApiParams   (name="search", type="varchar", required=true, description="搜索名称")
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
        $search = $this->request->get("search", "");
        if (empty($search)) {
            $this->error('请输入要搜索的内容');
        }
        $movieModel = new Movies;
        $params = [];
        $params["search"] = $search;
        $list = $movieModel->getMovieList($params, $page, $limit);

        $this->success('返回成功', $list);
    }

    /**
     * 搜索推荐
     *
     * @ApiTitle    (搜索推荐)
     * @ApiSummary  (搜索推荐)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/search/recommend)
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
    public function recommend()
    {
        $limit = $this->request->get("limit/d", 20);
        $page = $this->request->get("page/d", 1);
        $recommendModel = new RecommendModel;
        $params = [];
        $list = $recommendModel->getRecommendList($params, $page, $limit);

        $this->success('返回成功', $list);
    }

    /**
     * 为你推荐
     *
     * @ApiTitle    (为你推荐)
     * @ApiSummary  (为你推荐)
     * @ApiMethod   (GET)
     * @ApiRoute    (/api/search/recommendmovie)
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
    public function recommendmovie()
    {
        $limit = $this->request->get("limit/d", 20);
        $page = $this->request->get("page/d", 1);

        $movieModel = new Movies;

        $categoryId = $movieModel->categoryArr['recommend'] ?? 0;

        $list = $movieModel->getMovieList(["category_id" => $categoryId], $page, $limit);

        $this->success('返回成功', $list);
    }

}
