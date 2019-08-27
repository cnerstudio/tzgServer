<?php
namespace app\admin\validate;
use think\Validate;

class Pdd extends Validate{

	protected $rule=[
        'id' => 'require',
        'data' => 'require',
        'synbol' => 'require|in:1,2',
        'value' => 'require|number',
        'shop' => 'require'	
    ];
    // 定义场景
    protected $scene = [
        'upPrice'  =>  [
            'id',
            'data',
            'synbol',
            'value',
            'shop',
        ]
    ];
    protected $message=[
            'shop.require' => 'shop不能为空',
            'value.number' => '值不是数字',
            'synbol.in' => '符号错误',
    ];

}

