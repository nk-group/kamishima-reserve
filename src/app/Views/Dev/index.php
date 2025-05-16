<?= $this->extend('Layouts/admin-layout') ?>

<?= $this->section('content') ?>

    <h2><?= esc($page_title ?? '開発用テスト一覧') ?></h2>

    <p>以下のリンクから各機能のテストやデバッグを実行できます。</p>

    <ul class="list-group">
        <?php if (!empty($links) && is_array($links)): ?>
            <?php foreach ($links as $link): ?>
                <li class="list-group-item">
                    <a href="<?= esc($link['url'], 'attr') ?>"><?= esc($link['title']) ?></a>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <hr class="my-4">
    <div class="alert alert-danger" role="alert">
        <strong>注意:</strong> このページおよび関連する機能は開発環境専用です。本番環境ではアクセスできないようにしてください。
    </div>

<?= $this->endSection() ?>