<?php
    $roleDict = App\Consts\CommonConst::ROLE;
?>

<p>{{ $data['name'] }}さんのユーザー権限が管理者によって以下の通り変更されました。</p>
<p>変更前：{{ $roleDict[$data['before']] }}</p>
<p>変更後：{{ $roleDict[$data['after']] }}</p>