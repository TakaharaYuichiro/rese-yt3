<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AttendancesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function run()
    {
        // 登録されているユーザーIDのリスト
        $users = User::all();
        $userIds = [];
        foreach($users as $user){
            $userIds[] = $user->id;
        }

        // シーダーデータ作成条件
        $term_days = 30;    // 過去30日分のデータを作成
        $rest_count_max = 5;  // 1日最大5回まで休憩

        // 過去x日分のデータを作成
        for($d=$term_days; $d>0; $d--){
            $targetDate = strtotime(date("Y-m-d",strtotime("-{$d} day")));

            // 登録されている全てのユーザーに対して1日分の勤怠データを作成
            foreach($userIds as $userId) {
                $startTime = rand(5*60*60, 24*60*60);
                if ($startTime > 20*60*60) {
                    // 始業時刻が20時以降だったら、その日は休暇とみなす。一応、24時の値を入れおく
                    $startTime = 24*60*60;
                }
    
                $workingTime = rand(0, 12*60*60);

                $endTime = $startTime + $workingTime;
                if ($endTime > 24*60*60) {
                    // 始業時刻が24時以降だったら、打刻漏れとみなす（もしくはその日は休暇）
                    $endTime = 24*60*60;
                }
    
                $restCounts = rand(0, $rest_count_max);  
                $restTimeList = [];
                $lapTime = $startTime;
                for($i=0; $i<$restCounts; $i++){
                    $restStartTime = $lapTime + rand(1*60*60,3*60*60);
                    if($restStartTime >= $endTime){
                        break;
                    }
    
                    $restEndTime = $restStartTime + rand(0,1*60*60);
                    if ($restEndTime >= $endTime){
                        $restEndTime = $endTime;
                    }
                    $restTimePair = [$restStartTime, $restEndTime];
                    $restTimeList[] = $restTimePair;
    
                    $lapTime = $restEndTime;
                }   
    
                // 始業時刻の記録
                if ($startTime < 24*60*60){
                    // 23:59:59までなら記録。24時を超えていたら休暇とみなして記録しない
                    $targetDateTimeStr = date("Y-m-d H:i:s", $targetDate + $startTime);
                    $param = [
                        'user_id' => $userId,
                        'content_index' => 1,   // 始業時刻
                        'created_at' => $targetDateTimeStr,
                        'updated_at' => now(),
                    ];
                    DB::table('attendances')->insert($param);
                }
                
                // 休憩時刻の記録
                foreach($restTimeList as $restTimePair){
                    $restStartTime = $restTimePair[0];
                    if ($restStartTime < 24*60*60) {
                        $targetDateTimeStr = date("Y-m-d H:i:s", $targetDate + $restStartTime);
                        $param = [
                            'user_id' => $userId,
                            'content_index' => 3,   // 休憩開始/終了時刻
                            'created_at' => $targetDateTimeStr,
                            'updated_at' => now(),
                        ];
                        DB::table('attendances')->insert($param);
    
                        $restEndTime = $restTimePair[1];
                        if ($restEndTime < 24*60*60) {
                            $targetDateTimeStr = date("Y-m-d H:i:s", $targetDate + $restEndTime);
                            $param = [
                                'user_id' => $userId,
                                'content_index' => 3,   // 休憩開始/終了時刻
                                'created_at' => $targetDateTimeStr,
                                'updated_at' => now(),
                            ];
                            DB::table('attendances')->insert($param);
                        }
                    }
                }
    
                // 終業時刻の記録
                if ($endTime < 24*60*60) {
                    // 23:59:59までなら記録。24時を超えていたら休暇もしくは打刻漏れとみなして記録しない
                    $targetDateTimeStr = date("Y-m-d H:i:s", $targetDate + $endTime);
                    $param = [
                        'user_id' => $userId,
                        'content_index' => 2,   // 就業時刻
                        'created_at' => $targetDateTimeStr,
                        'updated_at' => now(),
                    ];
                    DB::table('attendances')->insert($param);
                }
            }
        }
    }
}
