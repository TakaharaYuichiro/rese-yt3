<?php

return [
    //paginate
    'PAGINATE'=>[
        'LINK_NUM' => '7', //paginationの一度に表示するリンクの数
    ],

    //飲食店画像ファイルの保存場所
    'image_storage' => env('IMAGE_STORAGE', 'public'),
];