<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class MemberCard extends Model
{
    protected $table = 'member_card';
    protected $primaryKey = 'id';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'id', 'card_name', 'card_alias', 'card_number', 'card_password',
        'amount', 'balance', 'term', 'card_status', 'user_type',
        'sys_user_id', 'loginname', 'active_date', 'create_time',
        'create_user_id', 'card_type', 'end_date', 'note', 'is_delete'
    ];

    /**
     * 验证会员卡登录
     */
    public static function validateCard($cardNumber, $password)
    {
        return DB::table('member_card')
            ->where('card_number', $cardNumber)
            ->where('card_password', $password)
            ->where('is_delete', '0')    // 未删除
            ->first();
    }
}