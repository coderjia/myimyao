<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Resource extends Model
{
    protected $table = 'resource';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'resource_name', 'resource_description', 'resource_image_url',
        'resource_url', 'type', 'operator_id', 'operator_name',
        'operator_type', 'upload_time', 'like_count', 'is_delete',
        'check_state', 'check_opinion', 'check_time', 'data_authority',
        'pv', 'reply_count'
    ];

    /**
     * 获取视频列表（分页）
     */
    public static function getVideoList($page = 1, $perPage = 27)
    {
        $offset = ($page - 1) * $perPage;
        
        return DB::table('resource')
            ->select('id', 'resource_name', 'resource_image_url', 'upload_time')
            ->whereIn('type', ['0', '3'])           // 视频类型
            ->where('is_delete', '0')      // 未删除
            ->where('check_state', '1')    // 审核通过
            ->whereIn('data_authority', ['1', '2'])  // 医生可看或全部可看
            ->orderBy('upload_time', 'desc')
            ->offset($offset)
            ->limit($perPage)
            ->get();
    }

    /**
     * 获取视频详情
     */
    public static function getVideoDetail($id)
    {
        return DB::table('resource')
            ->where('id', $id)
            ->where('type', '0')           // 视频类型
            ->where('is_delete', '0')      // 未删除
            ->where('check_state', '1')    // 审核通过
            ->whereIn('data_authority', ['1', '2'])  // 医生可看或全部可看
            ->first();
    }
}