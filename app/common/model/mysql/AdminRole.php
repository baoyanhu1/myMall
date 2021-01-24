<?php
namespace app\common\model\mysql;


use think\Model;

class AdminRole extends BaseModel
{
    /**
     * 关联AdminRole表
     * @return AdminRole|void
     */
    public function AdminRole()
    {
        return $this->hasOne(AdminRole::class,'role_id');
    }
}