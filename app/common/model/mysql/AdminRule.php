<?php


namespace app\common\model\mysql;


class AdminRule extends BaseModel
{
    /**
     * 获取侧边栏信息
     * @param string $field
     * @param mixed $status
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getAllPermissions($field = "*",$status = "")
    {
        $status = !empty($status) ? $status : config("status.mysql.table_normal");
        $where = ['status' => $status];
        return $this->field($field)->where($where)->select();

    }
}