<?php // app/Views/Admin/dashboard.php ?>
<?= $this->extend('Layouts/admin-layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('page_title') // レイアウトの $page_title を設定 ?>
    <?= esc($page_title ?? 'ダッシュボード') ?>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
        <h1 class="h2">ダッシュボード</h1>
    </div>

    <p>ようこそ、管理画面へ。ここはログイン後のフロントページ（ダッシュボード）です。</p>

    <div class="row mt-4">
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    今日の予約状況
                </div>
                <div class="card-body">
                    <h5 class="card-title">予約件数: X件</h5>
                    <p class="card-text">うち、Clear車検: Y件 (仮)</p>
                    <a href="<?= site_url('admin/reservations') ?>" class="btn btn-primary">詳細を見る</a>
                </div>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <div class="card">
                <div class="card-header">
                    お知らせ
                </div>
                <div class="card-body">
                    <h5 class="card-title">システムメンテナンス (仮)</h5>
                    <p class="card-text">XX月XX日 AM2:00 - AM4:00 にメンテナンスを実施します。</p>
                    <a href="#" class="btn btn-info">過去のお知らせ</a>
                </div>
            </div>
        </div>
    </div>

    <h2 class="mt-5">サンプルフォーム</h2>
    <form>
        <div class="mb-3">
            <label for="exampleInputEmail1" class="form-label">メールアドレス</label>
            <input type="email" class="form-control" id="exampleInputEmail1" aria-describedby="emailHelp">
            <div id="emailHelp" class="form-text">あなたのメールアドレスを他の誰かと共有することはありません。</div>
        </div>
        <div class="mb-3">
            <label for="exampleInputPassword1" class="form-label">パスワード</label>
            <input type="password" class="form-control" id="exampleInputPassword1">
        </div>
        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="exampleCheck1">
            <label class="form-check-label" for="exampleCheck1">記憶する</label>
        </div>
        <button type="submit" class="btn btn-primary">送信</button>
    </form>

    <?php if (auth()->loggedIn()): ?>
        <div class="mt-5 p-3 border rounded bg-light">
            <h3 class="h5">ログイン情報</h3>
            <p class="mb-1">ユーザー: <?= esc(auth()->user()->full_name ?? (auth()->user()->username ?? auth()->user()->email)) ?></p>
            <p class="mb-0">グループ: <?= implode(', ', auth()->user()->getGroups()) ?></p>
        </div>
    <?php endif; ?>

<?= $this->endSection() ?>

<?php // このページ固有の <head> 内アセット (例: CSS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_head_end') ?>
    <-- <link rel="stylesheet" href="path/to/specific.css"> -->
<?= $this->endSection() */ ?>

<?php // このページ固有の </body> 直前アセット (例: JS) を追加する場合は以下を使用 ?>
<?php /* echo $this->section('page_specific_before_body_end') ?>
    <-- <script src="path/to/specific.js"></script> -->
<?= $this->endSection() */ ?>