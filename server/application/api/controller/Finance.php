<?php
/**
 * 充值提现API
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Finance extends Controller
{
    private function getUserId()
    {
        $token = $this->request->header('token');
        if (!$token) return 0;
        $user = Db::name('member')->where('token', $token)->find();
        return $user ? $user['id'] : 0;
    }
    
    /**
     * 充值列表（金额档位）
     * GET /api/finance/recharge_list
     */
    public function rechargeList()
    {
        return json(['massage' => '', 'data' => [
            ['amount' => 100, 'gift' => 0],
            ['amount' => 200, 'gift' => 0],
            ['amount' => 500, 'gift' => 0],
            ['amount' => 1000, 'gift' => 0],
            ['amount' => 2000, 'gift' => 0],
            ['amount' => 5000, 'gift' => 0],
            ['amount' => 10000, 'gift' => 0],
            ['amount' => 50000, 'gift' => 0],
        ], 'status' => 1]);
    }
    
    /**
     * 提交充值
     * POST /api/finance/recharge_submit
     */
    public function rechargeSubmit()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $amount = $this->request->post('amount', 0);
        $type = $this->request->post('type', 'usdt');
        
        if ($amount <= 0) return json(['massage' => '金额错误', 'data' => [], 'status' => -1]);
        
        $orderSn = 'CZ' . date('YmdHis') . mt_rand(100, 999);
        
        Db::name('recharge')->insert([
            'order_sn'   => $orderSn,
            'uid'        => $uid,
            'amount'     => $amount,
            'type'       => $type,
            'status'     => 0,
            'createtime' => time()
        ]);
        
        // 返回收款地址
        return json(['massage' => '提交成功', 'data' => [
            'order_sn'  => $orderSn,
            'amount'    => $amount,
            'address'   => 'TXXXXXXXXXXXXXXX',  // 实际收货地址
            'qrcode'    => ''  // 收款码图片
        ], 'status' => 1]);
    }
    
    /**
     * 充值记录
     * GET /api/finance/recharge_log
     */
    public function rechargeLog()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $list = Db::name('recharge')->where('uid', $uid)->order('id', 'desc')->limit(50)->select();
        foreach ($list as &$item) {
            $item['time'] = date('Y-m-d H:i:s', $item['createtime']);
            $statusMap = ['待支付', '已完成', '失败'];
            $item['status_text'] = $statusMap[$item['status']] ?? '未知';
        }
        
        return json(['massage' => '', 'data' => $list, 'status' => 1]);
    }
    
    /**
     * 充值回调（模拟）
     * POST /api/finance/recharge_callback
     */
    public function rechargeCallback()
    {
        $orderSn = $this->request->post('order_sn');
        if (!$orderSn) return json(['massage' => '参数错误', 'data' => [], 'status' => -1]);
        
        $order = Db::name('recharge')->where('order_sn', $orderSn)->find();
        if (!$order || $order['status'] != 0) {
            return json(['massage' => '订单不存在或已处理', 'data' => [], 'status' => -1]);
        }
        
        Db::startTrans();
        try {
            Db::name('recharge')->where('id', $order['id'])->update(['status' => 1]);
            
            $member = Db::name('member')->find($order['uid']);
            $before = $member['money'];
            $after = $before + $order['amount'];
            
            Db::name('member')->where('id', $order['uid'])->setInc('money', $order['amount']);
            Db::name('member')->where('id', $order['uid'])->setInc('usdt_money', $order['amount']);
            
            Db::name('money_log')->insert([
                'uid'       => $order['uid'],
                'money'     => $order['amount'],
                'before'    => $before,
                'after'     => $after,
                'memo'      => "充值到账 {$order['amount']}",
                'type'      => 'recharge',
                'createtime' => time()
            ]);
            
            Db::commit();
            return json(['massage' => '处理成功', 'data' => [], 'status' => 1]);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['massage' => '处理失败', 'data' => [], 'status' => -1]);
        }
    }
    
    /**
     * 提交提现
     * POST /api/finance/withdraw_submit
     */
    public function withdrawSubmit()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $amount = $this->request->post('amount', 0);
        $type = $this->request->post('type', 'usdt');
        $address = $this->request->post('address', '');
        
        if ($amount <= 0 || !$address) {
            return json(['massage' => '参数错误', 'data' => [], 'status' => -1]);
        }
        
        $minWithdraw = 100;
        if ($amount < $minWithdraw) {
            return json(['massage' => "最低提现 ¥{$minWithdraw}", 'data' => [], 'status' => -1]);
        }
        
        $member = Db::name('member')->find($uid);
        if ($member['money'] < $amount) {
            return json(['massage' => '余额不足', 'data' => [], 'status' => -1]);
        }
        
        $orderSn = 'TX' . date('YmdHis') . mt_rand(100, 999);
        
        Db::startTrans();
        try {
            Db::name('member')->where('id', $uid)->setDec('money', $amount);
            Db::name('member')->where('id', $uid)->setInc('frozen_money', $amount);
            
            Db::name('withdraw')->insert([
                'order_sn'   => $orderSn,
                'uid'        => $uid,
                'amount'     => $amount,
                'type'       => $type,
                'address'    => $address,
                'status'     => 0,
                'createtime' => time()
            ]);
            
            Db::commit();
            return json(['massage' => '提现申请已提交，等待审核', 'data' => [], 'status' => 1]);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['massage' => '提交失败', 'data' => [], 'status' => -1]);
        }
    }
    
    /**
     * 提现记录
     * GET /api/finance/withdraw_log
     */
    public function withdrawLog()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $list = Db::name('withdraw')->where('uid', $uid)->order('id', 'desc')->limit(50)->select();
        foreach ($list as &$item) {
            $item['time'] = date('Y-m-d H:i:s', $item['createtime']);
            $statusMap = ['审核中', '已完成', '已拒绝'];
            $item['status_text'] = $statusMap[$item['status']] ?? '未知';
        }
        
        return json(['massage' => '', 'data' => $list, 'status' => 1]);
    }
}
