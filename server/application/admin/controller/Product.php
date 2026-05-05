<?php
/**
 * 产品管理控制器（后台）
 */
namespace app\admin\controller;

use app\common\controller\Backend;

class Product extends Backend
{
    protected $model = null;
    protected $relationModel = 'product';
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Product();
    }
    
    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $total = $this->model
                ->with('type')
                ->where($where)
                ->order($sort, $order)
                ->count();
                
            $list = $this->model
                ->with('type')
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                
            return json(['total' => $total, 'rows' => $list]);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->model->save($params);
            $this->success('添加成功');
        }
        $types = \app\common\model\ProductType::where('status', 'normal')->select();
        $this->assign('types', $types);
        return $this->view->fetch();
    }
    
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->model->where('id', $ids)->update($params);
            $this->success('更新成功');
        }
        $this->assign('row', $this->model->find($ids));
        $types = \app\common\model\ProductType::where('status', 'normal')->select();
        $this->assign('types', $types);
        return $this->view->fetch();
    }
    
    public function delete($ids = null)
    {
        $this->model->where('id', 'in', $ids)->delete();
        $this->success('删除成功');
    }
}

/**
 * 产品分类控制器
 */
class ProductType extends Backend
{
    protected $model = null;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\ProductType();
    }
    
    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $total = $this->model->where($where)->count();
            $list = $this->model->where($where)->order($sort, $order)->limit($offset, $limit)->select();
            
            return json(['total' => $total, 'rows' => $list]);
        }
        return $this->view->fetch();
    }
    
    public function add()
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->model->save($params);
            $this->success('添加成功');
        }
        return $this->view->fetch();
    }
    
    public function edit($ids = null)
    {
        if ($this->request->isPost()) {
            $params = $this->request->post('row/a');
            $this->model->where('id', $ids)->update($params);
            $this->success('更新成功');
        }
        $this->assign('row', $this->model->find($ids));
        return $this->view->fetch();
    }
}
