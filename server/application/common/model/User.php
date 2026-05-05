<?php
/**
 * 用户模型（Common层）
 * 包含用户常用查询方法
 */
namespace app\common\model;

use think\Model;

class User extends Model
{
    protected $name = 'user';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    // 自动完成
    protected $auto = [];
    protected $insert = ['jointime'];
    
    protected function setJointimeAttr($value)
    {
        return time();
    }
    
    protected function setPasswordAttr($value)
    {
        $salt = random(16);
        return [$salt, md5(md5($value) . $salt)];
    }
    
    /**
     * 通过邀请码查找用户
     */
    public function getByInviteCode($code)
    {
        return $this->where('invite_code', $code)->find();
    }
    
    /**
     * 生成唯一邀请码
     */
    public function generateInviteCode()
    {
        $code = '';
        while (true) {
            $code = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 8));
            if (!$this->where('invite_code', $code)->find()) {
                break;
            }
        }
        return $code;
    }
    
    /**
     * 更新用户余额
     */
    public function changeMoney($userId, $amount, $memo = '', $type = 'system')
    {
        $this->startTrans();
        try {
            $user = $this->lock(true)->find($userId);
            if (!$user) {
                throw new \Exception('用户不存在');
            }
            
            $before = $user->money;
            $after = bcadd($before, $amount, 2);
            
            // 更新余额
            $this->where('id', $userId)->setInc('money', $amount);
            
            // 记录日志
            $log = new UserMoneyLog();
            $log->save([
                'user_id'    => $userId,
                'money'      => $amount,
                'before'     => $before,
                'after'      => $after,
                'memo'       => $memo,
                'type'       => $type,
                'createtime' => time()
            ]);
            
            $this->commit();
            return true;
        } catch (\Exception $e) {
            $this->rollback();
            return false;
        }
    }
}
