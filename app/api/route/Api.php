<?php

use think\facade\Route;
//基础的路由定义方式
//Route::rule('users','User/index');
//资源控制器定义方式
Route::resource('user','User');
//根据二级栏目id获取三级栏目路由
Route::rule('subcategory/:id','category/sub');
//获取前端分类商品
Route::rule('lists','mall.lists/index');
//获取商品详情
Route::rule('detail/:id','mall.detail/index');
//获取抢购商品详情
Route::rule('spikeDetail/:id','mall.spikeDetail/index');
//创建订单
Route::resource("order","order.index");
//秒杀商品订单页获取秒杀商品信息
Route::rule("spikeOrder/:id","order.spike/index");