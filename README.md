# 勤怠管理システム Atte(アット)

ブラウザ上で自分の出退勤時間や休憩時間を打刻したり、グループのメンバーの勤怠データを閲覧するためのシステムです。

|![sample image](readme_fig/screenshot_index.png)|
|:-:|

## 作成した目的

出退勤・休憩などの勤怠データを記録することはとても面倒です。PCやスマホなどを利用して簡単にデータを記録できるシステムは、業務効率の改善につながります。

## アプリケーションURL

- 開発環境：<http://localhost/>
- phpMyAdmin：<http://localhost:8080>
- MailHog：<http://localhost:8025> (ブラウザからMailHog管理画面にアクセスするためのURL)

## 他のリポジトリ

なし

## 機能一覧

- 会員登録 (MailHogを利用した擬似的なメール認証機能付き)
- ログイン/ログアウト
- 勤務開始/勤務終了登録
- 休憩開始/休憩終了登録
- 日付別勤怠情報表示 (ページネーション5件ずつ)
- 会員別勤怠情報表示 (直近1ヶ月分、ページネーション7件ずつ)

## 使用技術(実行環境)

- PHP 8.3.10
- Laravel 8.83.8
- MySQL 8.0.26
  
## テーブル設計

![TABLE SPECIFICATION](readme_fig/table_specifications.png)

## ER図

![ER DIAGRAM](readme_fig/er_diagrams.png)

## 環境構築

Dockerビルド

1. `git clone <git@github.com>:TakaharaYuichiro/atte-yt.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

> *MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
エラーが発生する場合は、docker-compose.ymlファイルの「mysql」内に「platform」の項目を追加で記載してください*

``` bash
mysql:
    platform: linux/x86_64(この文追加)
    image: mysql:8.0.26
    environment:
```

Laravel環境構築

1. `docker-compose exec php bash`
2. `composer install`
3. 「.env.example」ファイルをコピーし「.env」に名称を変更。または、新しく.envファイルを作成
4. .envに以下の環境変数を追加

``` text
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=laravel_db
DB_USERNAME=laravel_user
DB_PASSWORD=laravel_pass

MAIL_MAILER=smtp
MAIL_HOST=mailhog
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="test_mail@ex.com"  # MailHog送信テスト用
MAIL_FROM_NAME="${APP_NAME}"
```

5. アプリケーションキーの作成

``` bash
php artisan key:generate
```

6. マイグレーションの実行

``` bash
php artisan migrate
```

7. シーディングの実行

``` bash
php artisan db:seed
```

## 動作テスト用の会員登録方法

1. ブラウザからlocalhostにアクセスし、Atteアプリのログイン画面で「会員登録」のリンクボタンをクリックしてください。
2. 会員登録画面でテスト用の名前とメールアドレス、パスワードを入力してください (テスト用のため、メールアドレスは適当な内容でかまいません)。
3. 同じく会員登録画面で「会員登録」のボタンをクリックすると、MailHogに確認メールが送信されます (この時点では会員登録は終了していません)。
4. ブラウザで別のタブを開き、localhost:8025にアクセスして、MailHogを起動してください。
5. MailHogに届いた「【Atte】メールアドレスの確認確認」のメールを開き、メールに記載されているURLリンクをクリックしてください。
6. 会員登録が完了するとAtteアプリのホーム画面が表示されます。

