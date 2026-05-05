<?php
/**
 * 会员管理控制器（后台）
 */
namespace app\admin\controller;

use app\common\controller\Backend;
use app\common\model\User;

class User extends Backend
{
    protected $model = null;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new User();
    }
    
    /**
     * 查看
     */
    public function index()
    {
        if ($this->request->isAjax()) {
            list($where, $sort, $order, $offset, $limit) = $this->buildparams();
            
            $total = $this->model
                ->where($where)
                ->order($sort, $order)
                ->count();
                
            $list = $this->model
                ->where($where)
                ->order($sort, $order)
                ->limit($offset, $limit)
                ->select();
                
            return json(['total' => $total, 'rows' => $list]);
        }
        return $this->view->fetch();
    }
    
    /**
     * 编辑用户
     */
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
    
    /**
     * 禁用用户
     */
    public function disable($ids = null)
    {
        $this->model->where('id', $ids)->update(['status' => 'hidden']);
        $this->success('已禁用');
    }
    
    /**
     * 启用用户
     */
    public function enable($ids = null)
    {
        $this->model->where('id', $ids)->update(['status' => 'normal']);
        $this->success('已启用');
    }
    
    /**
     * 资金操作
     */
    public function money()
    {
        if ($this->request->isPost()) {
            $id = $this->request->post('id');
            $amount = $this->request->post('amount');
            $memo = $this->request->post('memo', '后台调整');
            
            $model = new User();
            $model->changeMoney($id, $amount, $memo, 'system');
            $this->success('操作成功');
        }
        return $this->view->fetch();
    }
}
