<?php
/**
 * 登录/注册API
 */
namespace app\api\controller;

use think\Controller;
use app\common\model\Member;

class Login extends Controller
{
    /**
     * 登录
     * POST /api/login/login
     * @param string account 账号
     * @param string passwd 密码
     */
    public function login()
    {
        $account = $this->request->post('account');
        $passwd = $this->request->post('passwd');
        
        if (!$account || !$passwd) {
            return json(['massage' => '请输入用户名！', 'data' => [], 'status' => -1]);
        }
        
        $member = Member::where('username', $account)->find();
        if (!$member) {
            return json(['massage' => '用户名不存在！', 'data' => [], 'status' => -1]);
        }
        
        if (md5(md5($passwd) . $member['salt']) !== $member['password']) {
            return json(['massage' => '密码错误！', 'data' => [], 'status' => -1]);
        }
        
        if ($member['status'] != 1) {
            return json(['massage' => '账号已被禁用！', 'data' => [], 'status' => -1]);
        }
        
        // 生成token
        $token = base64_encode(md5(uniqid(mt_rand(), true) . time()));
        Member::where('id', $member['id'])->update([
            'token'     => $token,
            'logintime' => time(),
            'loginip'   => $this->request->ip()
        ]);
        
        return json([
            'massage' => '登录成功！',
            'data'    => ['token' => $token],
            'status'  => 1
        ]);
    }
    
    /**
     * 注册
     * POST /api/login/register
     */
    public function register()
    {
        $account = $this->request->post('account');
        $passwd = $this->request->post('passwd');
        
        if (!$account || !$passwd) {
            return json(['massage' => '请输入密码！', 'data' => [], 'status' => -1]);
        }
        
        if (Member::where('username', $account)->find()) {
            return json(['massage' => '用户名已存在！', 'data' => [], 'status' => -1]);
        }
        
        $salt = substr(md5(uniqid(mt_rand(), true)), 0, 6);
        $code = $this->generateCode();
        
        Member::create([
            'username'    => $account,
            'password'    => md5(md5($passwd) . $salt),
            'salt'        => $salt,
            'real_name'   => '',
            'money'       => 0,
            'code'        => $code,
            'status'      => 1,
            'createtime'  => time()
        ]);
        
        return json(['massage' => '注册成功！', 'data' => [], 'status' => 1]);
    }
    
    private function generateCode()
    {
        while (true) {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6));
            if (!Member::where('code', $code)->find()) return $code;
        }
    }
}
