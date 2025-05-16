<?php // app/Views/Partials/_alert_messages.php ?>

<?php // 1. バリデーションエラー (配列) がある場合の表示 ?>
<?php if (session()->has('errors')) : ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <h5 class="alert-heading">入力内容にエラーがあります</h5>
        <ul class="mb-0">
            <?php foreach (session('errors') as $error) : ?>
                <li><?= esc($error) ?></li>
            <?php endforeach ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<?php // 2. 一般的なエラーメッセージ (文字列) がある場合の表示 ?>
<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= session('error') // コントローラで with('error', 'メッセージ') または setFlashdata('error', ...) で渡されたもの ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<?php // 3. 成功メッセージがある場合の表示 ?>
<?php if (session()->has('message')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= session('message') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>

<?php // 4. 警告メッセージがある場合の表示 (オプション) ?>
<?php if (session()->has('warning')): ?>
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <?= session('warning') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
<?php endif ?>