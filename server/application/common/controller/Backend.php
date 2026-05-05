<?php
/**
 * 公共控制器基类
 */
namespace app\common\controller;

use think\Controller;

class Backend extends Controller
{
    protected $model = null;
    protected $searchFields = '';
    protected $relationSearch = false;
    
    protected function initialize()
    {
        parent::initialize();
    }
    
    /**
     * 构建查询参数（FastAdmin风格）
     */
    protected function buildparams()
    {
        $offset = $this->request->get('offset', 0);
        $limit = $this->request->get('limit', 20);
        $sort = $this->request->get('sort', 'id');
        $order = $this->request->get('order', 'desc');
        $search = $this->request->get('search', '');
        
        $where = [];
        if ($search && $this->searchFields) {
            $fields = explode(',', $this->searchFields);
            $where[] = [implode('|', $fields), 'like', '%' . $search . '%'];
        }
        
        return [$where, $sort, $order, $offset, $limit];
    }
}
