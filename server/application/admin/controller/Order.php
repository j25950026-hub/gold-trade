<?php
/**
 * 订单管理控制器（后台）
 */
namespace app\admin\controller;

use app\common\controller\Backend;
use think\Db;
use app\common\model\Order as OrderModel;

class Order extends Backend
{
    protected $model = null;
    
    public function _initialize()
    {
        parent::_initialize();
        $this->model = new OrderModel();
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
     * 手动结算
     */
    public function settle()
    {
        $ids = $this->request->post('ids');
        if (!$ids) {
            $this->error('请选择订单');
        }
        
        $orderModel = new \app\common\model\Order();
        $result = $orderModel->settleOrders($ids);
        
        if ($result) {
            $this->success('结算成功');
        } else {
            $this->error('结算失败');
        }
    }
    
    /**
     * 统计
     */
    public function statistics()
    {
        $todayStart = strtotime(date('Y-m-d 00:00:00'));
        $todayEnd = strtotime(date('Y-m-d 23:59:59'));
        
        $data = [
            'today_orders' => $this->model->whereBetween('createtime', [$todayStart, $todayEnd])->count(),
            'today_amount' => $this->model->whereBetween('createtime', [$todayStart, $todayEnd])->sum('amount'),
            'pending_count' => $this->model->where('status', 'pending')->count(),
            'total_users'   => \app\common\model\User::count()
        ];
        
        return json($data);
    }
}
