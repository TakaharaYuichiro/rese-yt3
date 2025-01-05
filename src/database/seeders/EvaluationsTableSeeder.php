<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Evaluation;
use DateTime;

class EvaluationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $params = [
            [
                'user_id' => 6,
                'shop_id' => 1,
                'favorite' => true,
                'score' => 5,
                'comment' => '料理がとても美味しく、特に○○（料理名）は絶品でした！素材の新鮮さが伝わってきて、また来たいと思いました。', 
            ],
            [
                'user_id' => 6,
                'shop_id' => 2,
                'favorite' => true,
                'score' => 3,
                'comment' => 'スタッフの皆さんがとても親切で、丁寧な接客に感動しました。細やかな気遣いが感じられて、とても居心地が良かったです。', 
            ],
            [
                'user_id' => 6,
                'shop_id' => 5,
                'favorite' => true,
                'score' => 4,
                'comment' => '店内の雰囲気が落ち着いていて、デートや特別な日にもピッタリだと思います。インテリアのセンスが素敵で写真映えしました！', 
            ],
            [
                'user_id' => 6,
                'shop_id' => 6,
                'favorite' => false,
                'score' => 3,
                'comment' => 'このクオリティでこの価格は驚きです！量もたっぷりで、大満足できるお店でした。学生にもおすすめです。', 
            ],

            [
                'user_id' => 7,
                'shop_id' => 1,
                'favorite' => true,
                'score' => 4,
                'comment' => '子ども連れで訪れましたが、キッズメニューやベビーシートが用意されていて助かりました。家族での食事にピッタリです。', 
            ],
            [
                'user_id' => 7,
                'shop_id' => 2,
                'favorite' => true,
                'score' => 5,
                'comment' => '○○（料理名）という珍しい料理が食べられるのが魅力的でした。他ではなかなか味わえない体験ができて大満足！', 
            ],
            [
                'user_id' => 8,
                'shop_id' => 1,
                'favorite' => false,
                'score' => 3,
                'comment' => '駅から徒歩3分で、アクセスがとても良いのが便利です。仕事帰りにも立ち寄りやすい場所でした。', 
            ],
            
        ];

        foreach($params as $param) {
            $exists = (
                Evaluation::where('user_id', $param['user_id'])
                         -> where('shop_id', $param['shop_id'])
                         -> exists()
            );

            if (!$exists){
                $param['created_at'] =  new DateTime();
                $param['updated_at'] =  new DateTime();
                DB::table('evaluations')->insert($param);
            }
        }
    }
}
