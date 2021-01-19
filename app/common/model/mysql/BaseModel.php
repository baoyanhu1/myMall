<?php


namespace app\common\model\mysql;


use think\Model;

class BaseModel extends Model
{
    //    写入数据时自动写入创建时间和更新时间
    protected $autoWriteTimestamp = true;

    /**
     * 根据主键更新数据
     * @param $id
     * @param $data
     * @return bool
     */
    public function updateById($id,$data){
        $data['update_time'] = time();
        $where = [
            "id" => $id
        ];
        $result = $this->where($where)->save($data);
        return $result;
    }

    /**
     * 按规格属性ID获取规格属性信息
     * @param $specsId
     * @return \think\Collection
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\DbException
     * @throws \think\db\exception\ModelNotFoundException
     */
    public function getNormalInIds($specsId){
        $result = $this->whereIn("id",$specsId)
            ->where("status",config("status.mysql.table_normal"))
            ->select();
        return $result;
    }
}