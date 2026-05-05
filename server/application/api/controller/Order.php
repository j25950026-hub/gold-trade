<?php
/**
 * 交易订单API
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class Order extends Controller
{
    /**
     * 获取当前用户ID
     */
    private function getUserId()
    {
        $token = $this->request->header('token');
        if (!$token) return 0;
        $user = Db::name('member')->where('token', $token)->find();
        return $user ? $user['id'] : 0;
    }
    
    /**
     * 下单交易
     * POST /api/order/buy
     * @param int goods_id 产品ID
     * @param int type 1涨 2跌
     * @param int number 金额
     * @param int seconds 秒数
     */
    public function buy()
    {
        $uid = $this->getUserId();
        if (!$uid) {
            return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        }
        
        $goodsId = $this->request->post('goods_id', 0);
        $type = $this->request->post('type', 0); // 1涨 2跌
        $number = $this->request->post('number', 0);
        $seconds = $this->request->post('seconds', 180);
        
        if (!$goodsId || !in_array($type, [1, 2]) || $number <= 0) {
            return json(['massage' => '参数错误', 'data' => [], 'status' => -1]);
        }
        
        if (!in_array($seconds, [60, 180, 300])) {
            return json(['massage' => '合约时间错误', 'data' => [], 'status' => -1]);
        }
        
        $good = Db::name('goods')->find($goodsId);
        if (!$good || $good['status'] != 1) {
            return json(['massage' => '产品不存在或已下架', 'data' => [], 'status' => -1]);
        }
        
        $member = Db::name('member')->find($uid);
        if ($member['money'] < $number) {
            return json(['massage' => '余额不足', 'data' => [], 'status' => -1]);
        }
        
        $profitRatio = Db::name('config')->where('key', 'profit_ratio')->value('value') ?: 3;
        $endTime = (time() + $seconds) * 1000;
        
        // 随机订单号
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $orderSn = $good['code'] . substr(str_shuffle($chars), 0, 8);
        
        Db::startTrans();
        try {
            // 扣款
            $before = $member['money'];
            $after = $before - $number;
            
            Db::name('member')->where('id', $uid)->setDec('money', $number);
            
            // 创建订单
            Db::name('order')->insert([
                'order_sn'      => $orderSn,
                'uid'           => $uid,
                'code'          => $good['code'],
                'title'         => $good['title'],
                'open_price'    => $good['price'],
                'number'        => $number,
                'type'          => $type,
                'seconds'       => $seconds,
                'profit_ratio'  => $profitRatio,
                'endTime'       => $endTime,
                'remain_milli_seconds' => $seconds * 1000,
                'status'        => 0,
                'buy_time'      => date('Y-m-d H:i:s'),
                'ip'            => $this->request->ip(),
                'createtime'    => time()
            ]);
            
            // 资金流水
            Db::name('money_log')->insert([
                'uid'       => $uid,
                'money'     => -$number,
                'before'    => $before,
                'after'     => $after,
                'memo'      => "下单{$good['title']} " . ($type == 1 ? '涨' : '跌'),
                'type'      => 'order',
                'createtime' => time()
            ]);
            
            Db::commit();
            
            return json(['massage' => '下单成功', 'data' => [
                'order_sn' => $orderSn,
                'endTime'  => $endTime
            ], 'status' => 1]);
        } catch (\Exception $e) {
            Db::rollback();
            return json(['massage' => '下单失败', 'data' => [], 'status' => -1]);
        }
    }
    
    /**
     * 订单列表
     * GET /api/order/orderlist?status=3
     */
    public function orderlist()
    {
        $uid = $this->getUserId();
        if (!$uid) {
            return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        }
        
        $status = $this->request->get('status', 0); // 0全部 1未完成 2已完成 3已结算
        $page = $this->request->get('page', 1);
        $limit = 20;
        
        $query = Db::name('order')->where('uid', $uid);
        
        // 结算过期订单
        $this->settleOrders($uid);
        
        $query->order('id', 'desc');
        $total = $query->count();
        $list = $query->page($page, $limit)->select();
        
        $pages = ceil($total / $limit);
        
        foreach ($list as &$item) {
            $item['code'] = $item['code'] . '/' . $item['title'];
            $item['ploss'] = $item['ploss'] ?: 0;
            $item['endTime'] = (int)$item['endTime'];
            $item['remain_milli_seconds'] = max(0, $item['endTime'] - time() * 1000);
            $item['sell_time'] = $item['sell_time'] ?: '';
            $item['end_price'] = $item['end_price'] ?: $item['open_price'];
            $item['open_price'] = number_format($item['open_price'], 4);
            $item['end_price'] = number_format($item['end_price'], 4);
            $item['end_profit'] = $item['end_profit'] ?: 0;
        }
        
        return json(['massage' => '', 'data' => [
            'lists'       => $list,
            'page'        => $page,
            'currentPage' => $page,
            'lastPage'    => $pages
        ], 'status' => 1]);
    }
    
    /**
     * 结算过期订单
     */
    private function settleOrders($uid = 0)
    {
        $now = time() * 1000;
        $query = Db::name('order')->where('status', 0)->where('endTime', '<', $now)->where('endTime', '>', 0);
        if ($uid) $query->where('uid', $uid);
        
        $orders = $query->select();
        
        foreach ($orders as $order) {
            $good = Db::name('goods')->where('code', $order['code'])->find();
            if (!$good) continue;
            
            $endPrice = $good['price'];
            $openPrice = $order['open_price'];
            
            $isWin = false;
            $isDraw = abs((float)$endPrice - (float)$openPrice) < 0.0001;
            
            if (!$isDraw) {
                if ($order['type'] == 1) { // 涨
                    $isWin = $endPrice > $openPrice;
                } else { // 跌
                    $isWin = $endPrice < $openPrice;
                }
            }
            
            if ($isDraw) {
                // 平局退钱
                $status = 3;
                $ploss = 0;
                Db::name('member')->where('id', $order['uid'])->setInc('money', $order['number']);
            } elseif ($isWin) {
                $status = 1;
                $ploss = bcmul($order['number'], $order['profit_ratio'], 2);
                Db::name('member')->where('id', $order['uid'])->setInc('money', $order['number'] + $ploss);
            } else {
                $status = 2;
                $ploss = -$order['number'];
            }
            
            Db::name('order')->where('id', $order['id'])->update([
                'end_price'  => $endPrice,
                'ploss'      => $ploss,
                'status'     => $status,
                'sell_time'  => date('Y-m-d H:i:s'),
                'updatetime' => time()
            ]);
            
            // 更新用户盈亏
            if ($status == 1) {
                Db::name('member')->where('id', $order['uid'])->setInc('yk', $ploss);
                Db::name('member')->where('id', $order['uid'])->setInc('yk_today', $ploss);
            }
        }
    }
}
