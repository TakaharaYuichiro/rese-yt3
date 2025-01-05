# 飲食店予約システム Rese(リーズ)

ある企業のグループ会社の飲食店予約サービス用のシステムです。

|![sample image](readme_fig/screenshot_01_shop_all.png)|
|:-:|

## 作成した目的

外部の飲食店予約サービスは手数料を支払う必要があります。自社で予約サービスを持つことでコストダウンを図ります。

## アプリケーションURL

- 開発環境：<http://localhost/>
- phpMyAdmin：<http://localhost:8080>
- MailHog：<http://localhost:8025> (ブラウザからMailHog管理画面にアクセスするためのURL)

## 動作検証に必要なサイト

- Stripe：<https://stripe.com/>
飲食店予約時の料金のカード決済のデモに使用します。
Stripe公式サイトにて、ユーザー登録と、APIキーの取得を行ってください。

## 他のリポジトリ

なし

## 機能一覧

### 基本機能

- 会員登録 (MailHogを利用した擬似的なメール認証機能付き)
- ログイン/ログアウト
- ユーザー情報取得
- 飲食店お気に入り一覧取得
- 飲食店予約情報取得
- 飲食店一覧取得
- 飲食店詳細取得
- 飲食店お気に入り追加
- 飲食店お気に入り削除
- 飲食店予約情報追加
- 飲食店予約情報削除
- エリアで飲食店検索
- ジャンルで飲食店検索
- 店名/概要で飲食店のキーワード検索

### 追加実装機能

- 飲食店予約変更機能
- 飲食店評価機能
- 認証、予約時のバリデーション
- レスポンシブデザイン(ブレイクポイント768px)
- 管理画面(管理者向け、店舗代表者向け)
- 飲食店の画像をストレージに保存
- メールによる本人確認
- 管理画面から利用者にお知らせメール送信
- 予約当日の朝に予約情報のリマインダーメール送信
- 飲食店予約時の確認メールにQRコード表示
- Stripeを利用した決済機能

## 使用技術(実行環境)

- PHP 8.3.10
- Laravel 8.83.8
- MySQL 8.0.26
  
## テーブル設計

**usersテーブル**：管理者、店舗代表者を含むユーザー情報を記録するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_1.png)

**shopsテーブル**：飲食店情報を記録するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_2.png)

**managersテーブル**：店舗代表者のユーザーID(user_id)と、その店舗代表者が管理する店舗ID(shop_id)を記録するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_3.png)

**evaluationsテーブル**：お気に入り登録と評価機能に関するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_5.png)

**genresテーブル**：飲食店のジャンルを記録するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_6.png)

**reservations**テーブル：予約機能に関するテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_7.png)

**coursesテーブル**：予約機能の付加機能として追加したコースメニュー(※)の情報を記録するためのテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_8.png)
(※)コースメニュー：ここでは飲食店ごとに設定できる料理の品目と定義。
Stripe決済の際に金額の入力が必要なため、コースメニューの予約内容に応じた金額を使用することにした。

**reserved_corsesテーブル**：予約されたコースメニューの内容(単価、数量)を記録するためのテーブル
![TABLE SPECIFICATION](readme_fig/table_specifications_9.png)



## ER図

![ER DIAGRAM](readme_fig/er_diagrams.png)
※各テーブルのカラム名については"created_at"、"updated_at"の記載を省略した

## 環境構築

Dockerビルド

1. `git clone <git@github.com>:TakaharaYuichiro/atte-yt.git`
2. DockerDesktopアプリを立ち上げる
3. `docker-compose up -d --build`

> MacのM1・M2チップのPCの場合、`no matching manifest for linux/arm64/v8 in the manifest list entries`のメッセージが表示されビルドができないことがあります。
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

# Stripe APIキー
STRIPE_PUBLIC_KEY=pk_test_xxxxxxxxxxxxxxxxxxx
STRIPE_SECRET_KEY=sk_test_xxxxxxxxxxxxxxxxxxx
```

> Stripe APIキーはStripeのサイトから取得してください

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





## 動作テスト

### テスト用アカウント

シーディングにより、権限(管理者、店舗代表者、利用者)ごとに1つずつ、以下のの3つのテスト用アカウントが登録されています。

|権限|Email|Password|
|:---|:---|:---|
|管理者|admin@ex.com|test_pw1234|
|店舗代表者|manager@ex.com|test_pw1234|
|利用者|test@ex.com|test_pw1234|

### アプリ起動、ログイン

1. ブラウザでlocalhostにアクセスし、Reseアプリを起動してください。
2. 画面左上のハンバーガーメニューから「Login」をクリックしてください。
![sample image](readme_fig/ss_menu-to-login.png)
4. Login画面で、前項に記載のいずれかのテスト用アカウントのEmailとPasswordを入力し、「ログイン」ボタンをクリックしてください。

### 上記以外のアカウントの登録方法

上記のテスト用アカウント以外でテストする場合は、以下の方法により会員登録してアカウントを新規作成してください。

1. すでに他のユーザーでログインしている場合は、画面左上のハンバーガーメニューから「Logout」をクリックしてください。
2. ハンバーガーメニューから「Registration」をクリックしてください。
3. 会員登録画面でテスト用の名前とメールアドレス、パスワードを入力してください (テスト用のため、メールアドレスは適当な内容でかまいません)。
4. 同じく会員登録画面で「会員登録」のボタンをクリックすると、MailHogに確認メールが送信されます (この時点では会員登録は終了していません)。
5. ブラウザで別のタブを開き、localhost:8025にアクセスして、MailHogを起動してください。
6. MailHogに届いた「【Rese】メールアドレスの確認確認」のメールを開き、メールに記載されているURLリンクをクリックしてください。
7. 会員登録が完了するとReseアプリにログインできます。

> この方法で作成したユーザーの権限は「利用者」となります。権限を変更する方法は「権限の変更方法」の項を参照してください。

### 決済機能(Stripe)について

1. 事前にStripeのユーザー登録と、.envへのStripe APIキーの記載を行ってください。
2. 店舗一覧画面(ホーム画面)で、「仙人」「牛助」「戦慄」のいずれかの「詳しく見る」ボタンをクリックして、店舗詳細/新規予約画面に進んでください。

> 本アプリでStripeの決済機能を実行するには、「コースメニュー」が登録されている店舗で予約する必要があります。
> 「仙人」「牛助」「戦慄」の3店舗には、シーディングによりあらかじめコースメニューが登録されています。
> これら3店舗以外では、店舗代表者アカウントでログインしたのち、各店舗の設定画面でコースメニューを登録しておく必要があります。

3. 新規予約画面にて、いずれかのコースメニューの数量を1以上にしたうえで、「予約内容確認」をクリックしてください。
4. 「この内容で予約する」 → 「支払いへ」とクリックして、Stripe決済のカード入力画面に進み、テスト用のカード情報を入力のうえ、「支払い」をクリックしてください。

> テスト用のカード情報については、以下のStripe公式サイトに記載の情報を利用してください。
> Stripe DOCS：<https://docs.stripe.com/testing?locale=ja-JP/>

### 予約の変更・削除について

予約を変更・削除する方法は以下のとおりです。

1. 画面左上のハンバーガーメニューから「My Page」をクリックしてください。
2. 予約状況の項目に表示されている対象の予約の「変更」ボタンをクリックしてください。
3. 内Login画面で、前述の管理者アカウントのEmailとPasswordを入力し、「ログイン」ボタンをクリックしてください。
5. ハンバーガーメニューから「管理者ページ」をクリックしてください。
6. 管理者ページの上部にある検索バーを利用して、対象のユーザーを検索してください。
7. 対象ユーザーの権限列にある設定ボタンをクリックしてください。
8. 権限変更ダイアログにて、「変更後の権限」をセレクトボックスから選択のうえ、「設定」をクリックしてください。

> 変更・削除ができるのは、明日以降の予約のみです。本日分もしくは過去の予約は削除できません。

### 権限の変更方法 (※管理者権限必要)

ユーザーの権限を変更するには、「管理者」でログインし、以下の方法で対象のユーザーの権限を変更してください。

1. すでに他のユーザーでログインしている場合は、画面左上のハンバーガーメニューから「Logout」をクリックしてください。
2. 画面左上のハンバーガーメニューから「Login」をクリックしてください。
3. Login画面で、前述の管理者アカウントのEmailとPasswordを入力し、「ログイン」ボタンをクリックしてください。
4. ハンバーガーメニューから「管理者ページ」をクリックしてください。
5. 管理者ページの上部にある検索バーを利用して、対象のユーザーを検索してください。
6. 対象ユーザーの権限列にある設定ボタンをクリックしてください。
7. 権限変更ダイアログにて、「変更後の権限」をセレクトボックスから選択のうえ、「設定」をクリックしてください。

### 店舗代表者の担当店舗の変更方法 (※管理者権限必要)

ユーザーの権限を変更するには、「管理者」でログインし、以下の方法で対象のユーザーの権限を変更してください。

1. すでに他のユーザーでログインしている場合は、画面左上のハンバーガーメニューから「Logout」をクリックしてください。
2. 画面左上のハンバーガーメニューから「Login」をクリックしてください。
3. Login画面で、前述の管理者アカウントのEmailとPasswordを入力し、「ログイン」ボタンをクリックしてください。
4. ハンバーガーメニューから「管理者ページ」をクリックしてください。
5. 管理者ページの上部にある検索バーを利用して、対象のユーザーを検索してください。
6. 対象ユーザーの権限列にある設定ボタンをクリックしてください。
7. 権限変更ダイアログにて、「変更後の権限」をセレクトボックスから選択のうえ、「設定」をクリックしてください。

### 店舗情報の新規作成・編集方法 (※店舗代表者権限必要)


### リマインダーメールについて

追加実装項目の「リマインダー」機能については、本アプリではLaravelのタスクスケジュール機能を用いて、ローカルサーバーにて実行する方法としています。
リマインダーメール機能を実行する方法は以下のとおりです。

1. `docker-compose exec php bash`
2. php artisan schedule:work

また、リマインダーメール機能に関するコードは、以下のファイルに記載しています。
・メール送信用のバッチコマンド : app/Console/Command/SendReminderMails.php
・毎日7:00にバッチコマンドを実行するコード : app/Console/Kernel.php

・ローカルサーバーでphp artisan schedule:work で実行できることを確認済み
