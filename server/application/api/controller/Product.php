<?php
/**
 * 产品API控制器
 * 产品列表、价格、K线
 */
namespace app\api\controller;

use think\Controller;
use app\common\model\Product;
use app\common\model\ProductType;

class Product extends Controller
{
    protected $noNeedLogin = ['lists', 'detail', 'kline'];
    
    /**
     * 产品列表
     * GET /api/product/lists
     */
    public function lists()
    {
        $products = Product::where('status', 'normal')
            ->order('sort', 'asc')
            ->select();
            
        $types = ProductType::where('status', 'normal')
            ->order('weigh', 'asc')
            ->select();
            
        $result = [];
        foreach ($types as $type) {
            $items = [];
            foreach ($products as $p) {
                if ($p['type_id'] == $type['id']) {
                    $items[] = [
                        'id'            => $p['id'],
                        'name'          => $p['name'],
                        'image'         => $p['image'],
                        'price'         => $p['price'],
                        'change_percent' => $p['change_percent'],
                        'high'          => $p['high'],
                        'low'           => $p['low'],
                        'open'          => $p['open'],
                        'close'         => $p['close'],
                        'is_hot'        => $p['is_hot'],
                        'time_types'    => ['1min', '3min', '5min'],
                        'min_amount'    => $p['min_amount'],
                        'max_amount'    => $p['max_amount'],
                        'odds'          => $p['odds']
                    ];
                }
            }
            if (!empty($items)) {
                $result[] = [
                    'id'   => $type['id'],
                    'name' => $type['name'],
                    'list' => $items
                ];
            }
        }
        
        return json(['code' => 1, 'data' => $result]);
    }
    
    /**
     * 产品详情
     * GET /api/product/detail?id=1
     */
    public function detail()
    {
        $id = $this->request->get('id');
        $product = Product::find($id);
        if (!$product) {
            return json(['code' => 0, 'msg' => '产品不存在']);
        }
        
        return json(['code' => 1, 'data' => [
            'id'            => $product['id'],
            'name'          => $product['name'],
            'image'         => $product['image'],
            'price'         => $product['price'],
            'real_price'    => $product['real_price'] ?: $product['price'],
            'change_percent' => $product['change_percent'],
            'high'          => $product['high'],
            'low'           => $product['low'],
            'open'          => $product['open'],
            'min_amount'    => $product['min_amount'],
            'max_amount'    => $product['max_amount'],
            'odds'          => $product['odds'],
            'buy_fee'       => $product['buy_fee'],
            'sell_fee'      => $product['sell_fee']
        ]]);
    }
    
    /**
     * K线数据
     * GET /api/product/kline?product_id=1&type=1min
     */
    public function kline()
    {
        $productId = $this->request->get('product_id', 0);
        $timeType = $this->request->get('type', '1min');
        
        $product = Product::find($productId);
        if (!$product) {
            return json(['code' => 0, 'msg' => '产品不存在']);
        }
        
        // 从kline_data字段读取K线JSON
        // 如果没有则自动生成
        $klineData = $product['kline_data'];
        if ($klineData) {
            $data = json_decode($klineData, true);
            if (!empty($data[$timeType])) {
                return json(['code' => 1, 'data' => $data[$timeType]]);
            }
        }
        
        // 自动生成K线数据
        $data = $this->generateKline($product, $timeType);
        return json(['code' => 1, 'data' => $data]);
    }
    
    /**
     * 在线人数（假数据）
     * GET /api/product/online
     */
    public function online()
    {
        $min = \think\Config::get('site.online_user_min') ?: 50;
        $max = \think\Config::get('site.online_user_max') ?: 300;
        $count = mt_rand($min, $max);
        
        return json(['code' => 1, 'data' => ['online_count' => $count]]);
    }
    
    private function generateKline($product, $timeType)
    {
        $now = time();
        $basePrice = $product['price'];
        $interval = ['1min' => 60, '3min' => 180, '5min' => 300, '15min' => 900, '30min' => 1800, '60min' => 3600];
        $sec = $interval[$timeType] ?? 60;
        $count = 100;
        
        $data = [];
        $price = $basePrice;
        for ($i = $count; $i >= 0; $i--) {
            $t = $now - $i * $sec;
            $change = (mt_rand(-50, 50) / 1000) * $price;
            $open = $price;
            $close = $price + $change;
            $high = max($open, $close) + abs($change) * 0.3;
            $low = min($open, $close) - abs($change) * 0.3;
            $volume = mt_rand(100, 10000);
            
            $data[] = [
                'time'   => $t,
                'open'   => round($open, 2),
                'high'   => round($high, 2),
                'low'    => round($low, 2),
                'close'  => round($close, 2),
                'volume' => $volume
            ];
            $price = $close;
        }
        
        return $data;
    }
}
