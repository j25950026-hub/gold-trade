<?php
/**
 * 充值管理控制器（后台）
 */
namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;

class Recharge extends Backend
{
    protected $model = null;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new \app\common\model\Recharge();
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
     * 手动确认到账
     */
    public function confirm()
    {
        $ids = $this->request->post('ids');
        if (!$ids) {
            $this->error('请选择订单');
        }
        
        $ids = is_array($ids) ? $ids : explode(',', $ids);
        
        Db::startTrans();
        try {
            foreach ($ids as $id) {
                $order = $this->model->find($id);
                if ($order && $order['status'] == 'pending') {
                    $this->model->where('id', $id)->update([
                        'status'   => 'success',
                        'pay_time' => time()
                    ]);
                    
                    // 加余额
                    $userModel = new \app\common\model\User();
                    $userModel->changeMoney(
                        $order['user_id'],
                        $order['real_amount'],
                        "管理员确认充值 {$order['amount']} 元",
                        'recharge'
                    );
                }
            }
            Db::commit();
            $this->success('确认成功');
        } catch (\Exception $e) {
            Db::rollback();
            $this->error('操作失败：' . $e->getMessage());
        }
    }
    
    /**
     * 拒绝充值
     */
    public function reject()
    {
        $ids = $this->request->post('ids');
        if (!$ids) {
            $this->error('请选择订单');
        }
        
        $this->model->where('id', 'in', $ids)
            ->where('status', 'pending')
            ->update(['status' => 'failed']);
            
        $this->success('已拒绝');
    }
}
