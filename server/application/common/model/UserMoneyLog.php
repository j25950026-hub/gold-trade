<?php
/**
 * 余额变动日志模型
 */
namespace app\common\model;

use think\Model;

class UserMoneyLog extends Model
{
    protected $name = 'user_money_log';
    protected $autoWriteTimestamp = 'int';
    protected $createTime = 'createtime';
    protected $updateTime = false;
}
