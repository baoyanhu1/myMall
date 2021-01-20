<?php


namespace app\common\model\mysql;


use think\Model;

class AdminRule extends Model
{
    /**
     * 获取侧边栏信息
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getSidebarInfo()
    {
        return $this->field('id,title,pid,icon,href,target')->select()->toArray();
    }
}