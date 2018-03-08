<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Topic::class, function (Faker $faker) {

    //使用 sentence() 随机生成『小段落』文本用以填充 title 标题字段和 excerpt 摘录字段
    $sentence = $faker->sentence();

    //随机取一个月内的时间
    $updated_at = $faker->dateTimeThisMonth();

    // 传参为生成最大时间不超过，创建时间永远比更改时间要早
    $created_at = $faker->dateTimeThisMonth($updated_at);
    return [
        'title' => $sentence,
        'body' => $faker->text(),   //text() 方法会生成大段的随机文本
        'excerpt' => $sentence,
        'created_at' => $created_at,
        'updated_at' => $updated_at,
    ];
});
