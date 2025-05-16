<?php // app/Views/Admin/dashboard.php ?>
<?= $this->extend('Layouts/admin-layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('page_title') // レイアウトの $page_title を設定 ?>
    <?= esc($page_title ?? 'ダッシュボード') ?>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <h2>ようこそ、管理画面へ</h2>
    <p>ここはログイン後のフロントページ（ダッシュボード）です。</p>
    <?php if (auth()->loggedIn()): ?>
        <p>現在ログインしているユーザー: <?= esc(auth()->user()->email ?? '取得できません') ?></p>
        <p>所属グループ:</p>
        <ul>
            <?php foreach (auth()->user()->getGroups() as $group): ?>
                <li><?= esc($group) ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

<p>開発中の機能一覧（予定）:</p>
<ul>
    <li>予約状況カレンダー</li>
    <li>予約検索／一覧</li>
    <li>予約詳細画面</li>
    <li>予約確定メール送信</li>
    <li>入庫予定表印刷（PDF帳票）</li>
    <li>予約タグ印刷（PDF帳票）</li>
    <li>予約データCSV出力</li>
    <li>定休日管理</li>
    <?php if (auth()->user()->inGroup('admin')): ?>
        <li>ユーザーマスタ</li>
        <li>データ一括削除</li>
        <li>リマインドメール設定</li>
        <li>動作環境設定</li>
    <?php endif; ?>
</ul>

<?= $this->endSection() ?>

<?php // このページ固有の <head> 内アセット (例: CSS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_head_end') ?>
    <-- <link rel="stylesheet" href="path/to/specific.css"> -->
<?= $this->endSection() */ ?>

<?php // このページ固有の </body> 直前アセット (例: JS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_body_end') ?>
    <-- <script src="path/to/specific.js"></script> -->
<?= $this->endSection() */ ?>