<?php
namespace app\common\model;

use think\Model;

class Member extends Model
{
    protected $name = 'member';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = 'updatetime';
    
    public static function getByMobile($mobile)
    {
        return self::where('phone', $mobile)->find();
    }
}
