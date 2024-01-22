<?php



return [

    "raters" => [
        "web" => \App\Models\User::class,
    ],

    "targets" => [
        "product" => \App\Models\Product::class,
        "post" => \App\Models\Post::class
    ],

    "matcher" => [
        "web" => ["product", "post"]
    ]

];