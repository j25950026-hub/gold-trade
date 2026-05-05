<?php
/**
 * 用户API
 * GET /api/user/getUserInfo
 * GET /api/user/moneylog
 */
namespace app\api\controller;

use think\Controller;
use think\Db;

class User extends Controller
{
    /**
     * 获取用户信息
     * GET /api/user/getUserInfo
     * Header: token
     */
    public function getUserInfo()
    {
        $token = $this->request->header('token');
        if (!$token) {
            return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        }
        
        $user = Db::name('member')->where('token', $token)->find();
        if (!$user) {
            return json(['massage' => '登录已过期', 'data' => [], 'status' => -22]);
        }
        
        return json([
            'massage' => '',
            'data'    => [
                'id'            => $user['id'],
                'username'      => $user['username'],
                'real_name'     => $user['real_name'],
                'phone'         => $user['phone'],
                'money'         => $user['money'],
                'usdt_money'    => $user['usdt_money'],
                'yk'            => $user['yk'],
                'code'          => $user['code'],
                'yk_today'      => $user['yk_today'],
                'credit_score'  => $user['credit_score'],
                'is_auth'       => $user['is_auth'],
                'id_auth_error' => $user['id_auth_error']
            ],
            'status'  => 1
        ]);
    }
    
    /**
     * 资金明细
     * GET /api/user/moneylog
     */
    public function moneylog()
    {
        $uid = $this->getUserId();
        if (!$uid) {
            return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        }
        
        $page = $this->request->get('page', 1);
        $limit = 20;
        
        $list = Db::name('money_log')
            ->where('uid', $uid)
            ->order('createtime', 'desc')
            ->page($page, $limit)
            ->select();
            
        foreach ($list as &$item) {
            $item['time'] = date('Y-m-d H:i:s', $item['createtime']);
        }
        
        return json(['massage' => '', 'data' => $list, 'status' => 1]);
    }
    
    /**
     * 实名认证
     * POST /api/user/auth
     */
    public function auth()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $realName = $this->request->post('real_name');
        $idCard = $this->request->post('id_card');
        
        if (!$realName || !$idCard) {
            return json(['massage' => '请填写完整信息', 'data' => [], 'status' => -1]);
        }
        
        Db::name('member')->where('id', $uid)->update([
            'real_name' => $realName,
            'is_auth'   => 2
        ]);
        
        return json(['massage' => '认证成功', 'data' => [], 'status' => 1]);
    }
    
    /**
     * 修改密码
     * POST /api/user/changepwd
     */
    public function changepwd()
    {
        $uid = $this->getUserId();
        if (!$uid) return json(['massage' => '未登录', 'data' => [], 'status' => -22]);
        
        $oldPwd = $this->request->post('old_password');
        $newPwd = $this->request->post('new_password');
        
        if (!$oldPwd || !$newPwd) {
            return json(['massage' => '参数错误', 'data' => [], 'status' => -1]);
        }
        
        $user = Db::name('member')->find($uid);
        if (md5(md5($oldPwd) . $user['salt']) !== $user['password']) {
            return json(['massage' => '原密码错误', 'data' => [], 'status' => -1]);
        }
        
        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        Db::name('member')->where('id', $uid)->update([
            'password' => md5(md5($newPwd) . $salt),
            'salt'     => $salt
        ]);
        
        // 重新登录
        $token = base64_encode(md5(uniqid(mt_rand(), true) . time()));
        Db::name('member')->where('id', $uid)->update(['token' => $token]);
        
        return json(['massage' => '修改成功', 'data' => ['token' => $token], 'status' => 1]);
    }
    
    private function getUserId()
    {
        $token = $this->request->header('token');
        if (!$token) return 0;
        $user = Db::name('member')->where('token', $token)->find();
        return $user ? $user['id'] : 0;
    }
}
