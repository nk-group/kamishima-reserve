# 車検予約管理システム データベース仕様書

**目的:** 上嶋自動車様のデータベース仕様書です。

**環境仕様:**

* **OS:** Windows 11 Pro (24H2 推奨) + WSL2/Ubuntu
* **コンテナ化:** Docker / Docker Compose
* **PHP:** 8.1.x
* **フレームワーク:** CodeIgniter 4.6.x
* **データベース:** MySQL 5.7.x + phpMyAdmin
* **認証:** Shield v1.1.x
* **PDF:** mPDF v8.1.x
* **フロントエンド:** Vite 5.4.x + Flatpickr v4.6.x + SASS
* **エディタ:** VSCode

-----

**1. 予約状況区分 (reserve_statuses)**

* **日本語テーブル名:** 予約状況区分
* **物理テーブル名:** `reserve_statuses`
* **説明:** 予約のさまざまな状態（未確定、予約確定など）を管理します。
* **特記事項:** メンテナンス画面は無し

**テーブル構造**

| 論理名 (日本語) | 物理名 (英語)   | データ型             | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                                   |
| --------- | ---------- | ---------------- | -------- | --- | --- | ------ | ------- | ----- | ------------------------------------ |
| 予約状況ID    | id         | TINYINT UNSIGNED | ○        | ○   |     |        |         |       | 主キー (1:未確定, 2:予約確定, 3:作業完了, 9:キャンセル) |
| 予約状況コード   | code       | VARCHAR(30)      | ○        |     |     | ○      |         | ○     | プログラム用コード (例: pending, confirmed)    |
| 予約状況名     | name       | VARCHAR(50)      | ○        |     |     |        |         |       | 例: 未確定, 予約確定                         |
| 表示順       | sort_order | INT(5) UNSIGNED  | ○        |     |     |        | 0       |       | 並び順を制御するための数値                        |

**初期データ**

| 予約状況ID | 予約状況コード   | 予約状況名 | 表示順 |
| ------ | --------- | ----- | --- |
| 1      | pending   | 未確定   | 10  |
| 2      | confirmed | 予約確定  | 20  |
| 3      | completed | 作業完了  | 30  |
| 9      | canceled  | キャンセル | 90  |

-----

**2. 作業種別区分 (work_types)**

* **日本語テーブル名:** 作業種別区分
* **物理テーブル名:** `work_types`
* **説明:** 提供する作業の種別（Clear車検、一般整備など）を管理します。
* **特記事項:** メンテナンス画面は無し

**テーブル構造**

| 論理名 (日本語) | 物理名 (英語)        | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                               |
| --------- | --------------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | -------------------------------- |
| 作業種別ID    | id              | TINYINT UNSIGNED    | ○        | ○   |     |        |         |       | 主キー                              |
| 作業種別コード   | code            | VARCHAR(30)         | ○        |     |     | ○      |         | ○     | プログラム用コード (例: clear_shaken)      |
| 作業種別名     | name            | VARCHAR(30)         | ○        |     |     |        |         |       | 例: Clear車検, 一般整備                 |
| 有効フラグ     | active          | TINYINT(1) UNSIGNED | ○        |     |     |        | 1       |       | 1:有効, 0:無効                       |
| Clear車検予約 | is_clear_shaken | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       |       | Clear車検用の作業かを識別するフラグ (1:Clear車検) |
| タグ色       | tag_color       | VARCHAR(7)          | ○        |     |     |        | #ffffff |       | 例: #ff99cc, #99ccff              |
| 集計カテゴリ     | count_category  | VARCHAR(20)         | ○        |     |     |        | other   | ○     | 予約件数集計用カテゴリ                        |
| 表示順       | sort_order      | INT(5) UNSIGNED     | ○        |     |     |        | 0       |       | 並び順を制御するための数値                    |

**集計カテゴリ (count_category) 取りうる値**

| 値               | 説明              | 用途                    |
| --------------- | --------------- | --------------------- |
| clear_shaken    | Clear車検件数       | Clear車検の予約件数を集計      |
| general_shaken  | 車検件数（Clear車検除く） | 一般車検・リース車検等の予約件数を集計 |
| other           | その他件数           | 車検以外の予約件数を集計        |
| excluded        | 集計除外            | 調整枠等、件数集計から除外する     |

**初期データ**

| 作業種別ID | 作業種別コード             | 作業種別名         | 有効フラグ | Clear車検予約 | タグ色     | 集計カテゴリ        | 表示順 |
| ------ | ------------------- | ------------- | ----- | --------- | ------- | ------------- | --- |
| 1      | clear_shaken        | Clear車検       | 1     | 1         | #ff99cc | clear_shaken  | 10  |
| 2      | periodic_inspection | 定期点検          | 1     | 0         | #99ccff | other         | 20  |
| 3      | general_shaken      | 一般車検          | 1     | 0         | #ff99cc | general_shaken | 30  |
| 4      | general_maintenance | 一般整備          | 1     | 0         | #ffffff | other         | 40  |
| 5      | adjustment_clear    | 調整枠 (Clear車検) | 1     | 1         | #cccccc | excluded      | 50  |
| 6      | lease_schedule      | リーススケジュール点検   | 1     | 0         | #99cc99 | other         | 60  |
| 7      | lease_statutory     | リース法定点検       | 1     | 0         | #99cc99 | other         | 70  |
| 8      | lease_shaken        | リース車検         | 1     | 0         | #99cc99 | general_shaken | 80  |
| 9      | lease_maintenance   | リース整備         | 1     | 0         | #99cc99 | other         | 90  |
| 10     | bodywork            | 板金            | 1     | 0         | #ffcc66 | other         | 100 |
| 99     | other               | その他           | 1     | 0         | #ffffff | other         | 110 |

-----
-----

**3. 店舗マスタ (shops)**

* **日本語テーブル名:** 店舗マスタ
* **物理テーブル名:** `shops`
* **説明:** アプリケーション内で利用する店舗の情報を管理します。
* **特記事項:** メンテナンス画面は無し

**テーブル構造**

| 論理名 (日本語) | 物理名 (英語)       | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                 |
| --------- | -------------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | ------------------ |
| 店舗ID      | id             | TINYINT UNSIGNED    | ○        | ○   |     |        |         |       | 主キー                |
| 店舗名       | name           | VARCHAR(50)         | ○        |     |     |        |         |       | 例: 本店、モーターショップカミシマ |
| Clear車検対応 | is_clear_ready | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       |       | 1:Clear車検対応店       |
| 有効フラグ     | active         | TINYINT(1) UNSIGNED | ○        |     |     |        | 1       |       | 1:有効, 0:無効         |
| 表示順       | sort_order     | INT(5) UNSIGNED     | ○        |     |     |        | 0       |       | 並び順を制御するための数値      |

**初期データ**

| 店舗ID | 店舗名          | Clear車検対応 | 有効フラグ | 表示順 |
| ---- | ------------ | --------- | ----- | --- |
| 1    | Clear車検      | 1         | 1     | 10  |
| 2    | 本社工場         | 0         | 1     | 20  |
| 3    | モーターショップカミシマ | 0         | 1     | 30  |

-----

**4. 予約時間帯マスタ (time_slots)**

* **日本語テーブル名:** 予約時間帯マスタ
* **物理テーブル名:** `time_slots`
* **説明:** Clear車検対応店舗で提供される、標準的な予約時間枠を管理します。Clear車検の予約や調整枠の設定などに利用されます。
* **特記事項:** メンテナンス画面は無し

**テーブル構造**

| 論理名 (日本語) | 物理名 (英語)   | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                 |
| --------- | ---------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | ------------------ |
| 予約時間帯ID   | id         | TINYINT UNSIGNED    | ○        | ○   |     |        |         |       | 主キー                |
| 店舗ID      | shop_id    | TINYINT UNSIGNED    | ○        |     | ○   |        |         |       | `shops.id`への外部キー   |
| 予約時間帯名    | name       | VARCHAR(30)         | ○        |     |     |        |         |       | 例: 8:45～           |
| 開始時刻      | start_time | TIME                | ○        |     |     |        |         |       | 開始時刻 (例: 08:45:00) |
| 終了時刻      | end_time   | TIME                | ○        |     |     |        |         |       | 終了時刻 (例: 09:00:00) |
| 有効フラグ     | active     | TINYINT(1) UNSIGNED | ○        |     |     |        | 1       |       | 1:有効, 0:無効         |
| 表示順       | sort_order | INT(5) UNSIGNED     | ○        |     |     |        | 0       |       | 並び順を制御するための数値      |

**初期データ**

| 予約時間帯ID | 店舗ID | 予約時間帯名 | 開始時刻  | 終了時刻  | 有効フラグ | 表示順 |
| ------- | ---- | ------ | ----- | ----- | ----- | --- |
| 1       | 1    | 08:45～ | 08:45 | 09:29 | 1     | 10  |
| 2       | 1    | 09:30～ | 09:30 | 10:14 | 1     | 20  |
| 3       | 1    | 10:15～ | 10:15 | 10:59 | 1     | 30  |
| 4       | 1    | 11:00～ | 11:00 | 11:44 | 1     | 40  |
| 5       | 1    | 13:00～ | 13:00 | 13:44 | 1     | 50  |
| 6       | 1    | 13:45～ | 13:45 | 14:29 | 1     | 60  |
| 7       | 1    | 14:30～ | 14:30 | 15:14 | 1     | 70  |
| 8       | 1    | 15:15～ | 15:15 | 15:59 | 1     | 80  |
| 9       | 1    | 16:00～ | 16:00 | 16:44 | 1     | 90  |
| 10      | 1    | 16:45～ | 16:45 | 17:29 | 1     | 100 |

-----

**5. 車両種別マスタ (vehicle_types)**

* **日本語テーブル名:** 車両種別マスタ
* **物理テーブル名:** `vehicle_types`
* **説明:** 車両の種別（乗用車、商用車、貨物車など）を管理します。
* **特記事項:** メンテナンス画面が必要 (CRUD)

**テーブル構造**

| 論理名 (日本語)    | 物理名 (英語)   | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                         |
| ------------ | ---------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | -------------------------- |
| 車両種別ID       | id         | INT UNSIGNED        | ○        | ○   |     |        |         |       | 主キー、自動増分                   |
| 車両種別コード      | code       | VARCHAR(4)          | ○        |     |     | ○      |         | ○     | 数値4桁のコード                   |
| 車両種別名        | name       | VARCHAR(30)         | ○        |     |     |        |         |       | 例: 乗用車, 商用車, 貨物車           |
| 有効フラグ        | active     | TINYINT(1) UNSIGNED | ○        |     |     |        | 1       |       | 1:有効, 0:無効                 |
| 作成日時         | created_at | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |
| 更新日時         | updated_at | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |
| 削除日時 (論理削除用) | deleted_at | DATETIME            |          |     |     |        | NULL    |       | CodeIgniterのソフトデリート機能用     |

**初期データ**

| 車両種別ID | 車両種別コード | 車両種別名 | 有効フラグ |
| ------ | ------- | ----- | ----- |
| 1      | 9999    | その他   | 1     |
| 2      | 0001    | 乗用車   | 1     |
| 3      | 0002    | 商用車   | 1     |
| 4      | 0003    | 貨物車   | 1     |
| 5      | 0004    | 特殊用途車 | 1     |

-----

**6. 定休日マスタ (shop_closing_days)**

* **日本語テーブル名:** 定休日マスタ
* **物理テーブル名:** `shop_closing_days`
* **説明:** 定休日や休業日を管理するテーブルです。

**テーブル構造**

| 論理名 (日本語)    | 物理名 (英語)          | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                         |
| ------------ | ----------------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | -------------------------- |
| 定休日ID        | id                | INT UNSIGNED        | ○        | ○   |     |        |         | ○     | 主キー、自動増分                   |
| 店舗ID         | shop_id           | TINYINT UNSIGNED    | ○        |     | ○   |        |         | ○     | `shops.id` への外部キー          |
| 定休日名         | holiday_name      | VARCHAR(50)         | ○        |     |     |        |         |       | 例: 年末年始休業、毎週火曜日            |
| 開始日          | start_date        | DATE                | ○        |     |     |        |         | ○     | 休業開始日                      |
| 終了日          | end_date          | DATE                |          |     |     |        | NULL    | ○     | 休業終了日、単日の場合はNULL           |
| 繰り返しフラグ      | is_recurring      | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       |       | 1: 毎年繰り返し、0: 一度限り          |
| 曜日指定         | recurring_weekday | TINYINT UNSIGNED    |          |     |     |        | NULL    |       | 毎週特定曜日に適用する場合（1=日曜, 7=土曜）  |
| 作成日時         | created_at        | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |
| 更新日時         | updated_at        | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |
| 削除日時 (論理削除用) | deleted_at        | DATETIME            |          |     |     |        | NULL    |       | CodeIgniterのソフトデリート機能用     |

**初期データ**

| 定休日ID | 店舗ID | 定休日名               | 開始日  | 終了日  | 繰り返しフラグ | 曜日指定 |
| ----- | ---- | ------------------ | ---- | ---- | ------- | ---- |
| 1     | 1    | 定休日 (Clear車検)      | NULL | NULL | 1       | 4    |
| 2     | 2    | 定休日 (本社工場)         | NULL | NULL | 1       | 4    |
| 3     | 3    | 定休日 (モーターショップカミシマ) | NULL | NULL | 1       | 4    |

-----

**7. リマインドメール設定 (reminder_settings)**

* **日本語テーブル名:** リマインドメール設定
* **物理テーブル名:** `reminder_settings`
* **説明:** リマインドメールを送信するための情報管理します。※レコードの追加・削除機能は無し

**テーブル構造**

| 論理名 (日本語)  | 物理名 (英語)         | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                         |
| ---------- | ---------------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | -------------------------- |
| リマインドメールID | id               | INT UNSIGNED        | ○        | ○   |     |        |         | ○     | 主キー                        |
| 作業種別ID     | work_type_id     | TINYINT UNSIGNED    | ○        |     | ○   |        |         |       | `work_types.id`への外部キー      |
| 送信日数設定     | send_days_before | TINYINT UNSIGNED    |          |     |     |        |         |       | 点検予定日の何日前に送信するかを設定         |
| 差出人名       | sender_name      | VARCHAR(100)        |          |     |     |        |         |       | メールの差出人名                   |
| 差出人名メール    | sender_email     | VARCHAR(255)        |          |     |     |        |         |       | メールの差出人メールアドレス             |
| BCC        | bcc_recipients   | VARCHAR(255)        |          |     |     |        | NULL    |       | 複数宛先を設定する場合は;区切る           |
| メールタイトル    | email_subject    | VARCHAR(255)        |          |     |     |        | NULL    |       | メールタイトル                    |
| メール本文      | email_body       | TEXT                |          |     |     |        | NULL    |       | メール本文                      |
| 有効フラグ      | active           | TINYINT(1) UNSIGNED | ○        |     |     |        | 1       |       | 1:有効, 0:無効                 |
| 作成日時       | created_at       | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |
| 更新日時       | updated_at       | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定 |

**初期データ**

| リマインドメールID | 作業種別ID | 送信日数設定 | 差出人名  | 差出人名メール              | BCC | メールタイトル          | メール本文 | 有効フラグ |
| ---------- | ------ | ------ | ----- | -------------------- | --- | ---------------- | ----- | ----- |
| 1          | 1      | 90     | 上嶋自動車 | info@kamishima.co.jp |     | 自動車検査登録のお知らせ     |       | 1     |
| 2          | 2      | 60     | 上嶋自動車 | info@kamishima.co.jp |     | 定期点検のお知らせ        |       | 1     |
| 3          | 3      | 60     | 上嶋自動車 | info@kamishima.co.jp |     | 自動車検査登録のお知らせ     |       | 1     |
| 4          | 4      | 60     |       |                      |     |                  |       | 0     |
| 5          | 5      | 60     |       |                      |     |                  |       | 0     |
| 6          | 6      | 60     | 上嶋自動車 | info@kamishima.co.jp |     | リーススケジュール点検のお知らせ |       | 1     |
| 7          | 7      | 60     | 上嶋自動車 | info@kamishima.co.jp |     | リース法定点検のお知らせ     |       | 1     |
| 8          | 8      | 60     | 上嶋自動車 | info@kamishima.co.jp |     | リース車検のお知らせ       |       | 1     |
| 9          | 9      | 60     |       |                      |     |                  |       | 0     |
| 10         | 10     | 60     |       |                      |     |                  |       | 0     |
| 11         | 99     | 60     |       |                      |     |                  |       | 0     |

-----

**8. システム環境設定 (app_config)**

  * **日本語テーブル名:** システム環境設定
  * **物理テーブル名:** `app_config`
  * **説明:** 本システム共通で使う設定情報をキー・バリュー形式で管理します。
  * **特記事項:** 各設定値のデータ型はアプリケーション側で適切に解釈・バリデーションする必要があります。パスワード等の機密情報は暗号化して保存することを推奨します。

**テーブル構造**

| 論理名 (日本語) | 物理名 (英語)    | データ型         | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                                                   |
| --------- | ----------- | ------------ | -------- | --- | --- | ------ | ------- | ----- | ---------------------------------------------------- |
| 設定キー      | key         | VARCHAR(100) | ○        | ○   |     | ○      |         | ○     | 設定項目を識別するためのユニークなキー (例: `company.name`, `smtp.port`) |
| 設定値       | value       | TEXT         |          |     |     |        | NULL    |       | 設定値。様々な形式のデータ（文字列、数値、JSON文字列など）を格納。                  |
| 説明        | description | VARCHAR(255) |          |     |     |        | NULL    |       | 設定項目の説明や用途など（任意）。                                    |
| 作成日時      | created_at  | DATETIME     | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定                           |
| 更新日時      | updated_at  | DATETIME     | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定                           |

**初期データ (例)**

| 設定キー                          | 設定値                          | 説明                                                  |
| ----------------------------- | ---------------------------- | --------------------------------------------------- |
| app.name                      | 上嶋自動車 車検予約管理システム             | アプリケーション名                                           |
| company.name                  | 株式会社上嶋自動車                    | 運営会社名                                               |
| maintenance.mode              | 0                            | メンテナンスモード (0:OFF, 1:ON)                             |
| smtp.host                     | mail.example.com             | SMTPサーバーホスト名                                        |
| smtp.port                     | 587                          | SMTPサーバーポート番号                                       |
| smtp.user                     | user@example.com             | SMTP認証ユーザー名                                         |
| smtp.password                 | your_encrypted_password_here | SMTP認証パスワード (暗号化して保存)                               |
| smtp.encryption               | tls                          | SMTP暗号化方式 (例: none, ssl, tls) PHPMailer->SMTPSecure |
| reminder.default_sender_name  | 上嶋自動車                        | リマインドメールのデフォルト差出人名                                  |
| reminder.default_sender_email | info@kamishima.co.jp         | リマインドメールのデフォルト差出人メールアドレス                            |

-----

**9. 予約データ (reservations)**

* **日本語テーブル名:** 予約データ
* **物理テーブル名:** `reservations`
* **説明:** 車検や点検の予約情報を管理するテーブルです。

**テーブル構造**

| 論理名 (日本語)    | 物理名 (英語)                  | データ型                | NOT NULL | PK  | FK  | UNIQUE | DEFAULT | INDEX | 備考                                                |
| ------------ | ------------------------- | ------------------- | -------- | --- | --- | ------ | ------- | ----- | ------------------------------------------------- |
| 予約ID         | id                        | INT UNSIGNED        | ○        | ○   |     |        |         |       | 主キー、自動増分                                          |
| 予約番号         | reservation_no            | VARCHAR(8)          | ○        |     |     | ○      |         |       | 例: 25050001 (YYMMNNNN)                            |
| 予約ステータスID    | reservation_status_id     | TINYINT UNSIGNED    | ○        |     | ○   |        |         |       | `reserve_statuses.id`への外部キー                       |
| 予約確認用GUID   | reservation_guid          | VARCHAR(36)         | ○        |     |     | ○      |         | ○     | 顧客向け予約状況確認ページURL用GUID (例: UUID v4)         |
| 作業種別ID       | work_type_id              | TINYINT UNSIGNED    | ○        |     | ○   |        |         | ○     | `work_types.id`への外部キー                             |
| 作業店舗ID       | shop_id                   | TINYINT UNSIGNED    | ○        |     | ○   |        |         | ○     | `shops.id`への外部キー                                  |
| 予約希望日        | desired_date              | DATE                | ○        |     |     |        |         | ○     | 例: 2025年2月5日                                      |
| 予約希望時間帯ID    | desired_time_slot_id      | TINYINT UNSIGNED    |          |     | ○   |        | NULL    |       | `time_slots.id`への外部キー。予約時間帯マスタから選択する場合にのみ使用。      |
| 予約開始時刻       | reservation_start_time    | TIME                |          |     |     |        | NULL    |       | 予約希望時間帯IDがNULL以外の場合、time_slotsテーブルで定義した開始時間と同じにする |
| 予約終了時刻       | reservation_end_time      | TIME                |          |     |     |        | NULL    |       | 予約希望時間帯IDがNULL以外の場合、time_slotsテーブルで定義した終了時間と同じにする |
| お客様氏名        | customer_name             | VARCHAR(50)         | ○        |     |     |        |         | ○     | 例: 鈴木 一郎                                          |
| お客様カナ名       | customer_kana             | VARCHAR(50)         |          |     |     |        |         | ○     | 例: スズキ　イチロウ                                       |
| メールアドレス      | email                     | VARCHAR(255)        | ○        |     |     |        |         |       | 例: i.suzuki@sample.com                            |
| LINE識別名      | line_display_name         | VARCHAR(100)        |          |     |     |        | NULL    |       | LINE経由の場合の表示名                                     |
| LINE経由フラグ    | via_line                  | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       |       | 1: LINE経由, 0: その他 (デフォルトは0)                       |
| 連絡先電話番号      | phone_number1             | VARCHAR(20)         | ○        |     |     |        |         | ○     | 例: 090-XXXX-XXXX                                  |
| 電話番号2        | phone_number2             | VARCHAR(20)         |          |     |     |        | NULL    |       | 例: 0155-XX-XXXX                                   |
| 郵便番号         | postal_code               | VARCHAR(8)          |          |     |     |        | NULL    |       | 例: 080-0803                                       |
| 住所           | address                   | VARCHAR(255)        |          |     |     |        | NULL    |       | 例: 帯広市東３条南８丁目１－１ ＮＫビル                             |
| 車両ナンバー(地域)   | vehicle_license_region    | VARCHAR(10)         |          |     |     |        | NULL    |       | 例: 帯広                                             |
| 車両ナンバー(分類)   | vehicle_license_class     | VARCHAR(5)          |          |     |     |        | NULL    |       | 例: 330                                            |
| 車両ナンバー(がな)   | vehicle_license_kana      | VARCHAR(5)          |          |     |     |        | NULL    |       | 例: る                                              |
| 車両ナンバー(番号)   | vehicle_license_number    | VARCHAR(5)          | ○        |     |     |        |         | ○     | 例: 583                                            |
| 車両種別ID       | vehicle_type_id           | INT UNSIGNED        |          |     | ○   |        | NULL    |       | `vehicle_types.id`への外部キー                          |
| 車種名          | vehicle_model_name        | VARCHAR(50)         |          |     |     |        | NULL    |       | 例: スカイライン                                         |
| 初年度登録        | database_specification.md | DATE                |          |     |     |        | NULL    |       | 例：2020年10月                                        |
| 車検満了日        | shaken_expiration_date    | DATE                |          |     |     |        | NULL    |       | 例: 2025年2月20日                                     |
| 型式指定番号       | model_spec_number         | VARCHAR(10)         |          |     |     |        | NULL    |       | 例: 50506                                          |
| 類別区分番号       | classification_number     | VARCHAR(10)         |          |     |     |        | NULL    |       | 例: 0689                                           |
| 代車利用         | loaner_usage              | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       |       | 1: 利用する  0: 利用しない (デフォルトは0)                       |
| 代車名          | loaner_name               | VARCHAR(20)         |          |     |     |        | NULL    |       | 呼称や車番                                             |
| お客様要望      | customer_requests         | TEXT                |          |     |     |        | NULL    |       | お客様からのご要望事項                                     |
| メモ           | notes                     | TEXT                |          |     |     |        | NULL    |       | 車検に関する注意事項等                                       |
| 次回点検日        | next_inspection_date      | DATE                |          |     |     |        | NULL    |       | 今回の作業完了後に設定される、顧客への次回の推奨点検日                       |
| 次回点検案内送信     | send_inspection_notice    | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       | ○     | 次回の点検案内を顧客に送信する予定があるかを示すフラグ (1: 送信する, 0: 送信しない)   |
| 次回作業種別ID     | next_work_type_id         | TINYINT UNSIGNED    |          |     | ○   |        | NULL    |       | `work_types.id`への外部キー                             |
| 次回コンタクト予定日   | next_contact_date         | DATE                |          |     |     |        | NULL    | ○     | 次回フォローアップや連絡を行う予定日                                |
| 次回点検案内送信済    | inspection_notice_sent    | TINYINT(1) UNSIGNED | ○        |     |     |        | 0       | ○     | 次回の点検案内が顧客に送信済みであるかを示すフラグ (1: 送信済み, 0: 未送信)       |
| 作成日時         | created_at                | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定                        |
| 更新日時         | updated_at                | DATETIME            | ○        |     |     |        |         |       | CodeIgniterのタイムスタンプ機能で自動設定                        |
| 削除日時 (論理削除用) | deleted_at                | DATETIME            |          |     |     |        | NULL    |       | CodeIgniterのソフトデリート機能用                            |

環境設定
未来の予約
送信メールアカウント
