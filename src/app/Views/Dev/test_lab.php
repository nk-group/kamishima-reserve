<?= $this->extend('Layouts/admin-layout') ?> <?php // 共通レイアウトを継承 ?>

<?= $this->section('content') ?> <?php // レイアウトの 'content' セクションに表示 ?>

    <h2><?= esc($page_title ?? '汎用テスト結果') ?></h2>

    <?php if (!empty($executed_code_description)): ?>
        <div class="alert alert-info" role="alert">
            <strong>実行された処理の概要:</strong><br>
            <?= nl2br(esc($executed_code_description)) ?>
        </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">実行結果</h5>
        </div>
        <div class="card-body">
            <?php if (empty($outputResults)): ?>
                <p class="text-muted">出力結果はありません。<code>TestController::sandbox()</code> メソッド内の指定箇所にテストコードを記述してください。</p>
            <?php else: ?>
                <pre style="white-space: pre-wrap; word-wrap: break-word; background-color: #f8f9fa; padding: 15px; border: 1px solid #dee2e6; border-radius: 0.25rem; font-size: 0.9em;">
<?php
                    // print_r を使って整形表示 (セキュリティのため esc() でエスケープ)
                    echo esc(print_r($outputResults, true));

                    // var_export を使うと、よりPHPコードに近い形で表示できます
                    // echo esc(var_export($outputResults, true));
?>
                </pre>
            <?php endif; ?>
        </div>
    </div>

    <div class="alert alert-warning mt-4" role="alert">
        <h4 class="alert-heading">利用方法</h4>
        <p>
            このページは、開発中の「ちょっとしたテスト」を行うためのサンドボックスです。<br>
            テストしたいPHPコードは、<code>app/Controllers/Dev/TestController.php</code> ファイル内の <code>sandbox()</code> メソッドの中の指定された箇所に記述してください。
        </p>
        <hr>
        <p class="mb-0">
            実行結果は <code>$outputResults</code> 配列にキーと値のペアで格納することで、このページに表示されます。
        </p>
    </div>

    <hr class="my-4">
    <p><a href='<?= site_url('dev-test') ?>' class="btn btn-secondary btn-sm">テストコントローラー トップに戻る</a></p>

<?= $this->endSection() ?>