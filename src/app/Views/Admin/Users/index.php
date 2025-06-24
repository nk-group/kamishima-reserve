<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? 'ユーザーマスタ') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') // ページヘッダー用セクション ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-people-fill"></i> <?php // ユーザー管理に合うアイコンを追加 ?>
            <?= esc($h1_title) ?>
        </h1>
    </div>

<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <div class="page-content">
        <div class="results-section">
            <div class="results-header">
                <div class="results-count">登録ユーザー：<?= esc($totalUsers ?? 0) ?>件</div>
                <a href="<?= site_url('admin/users/new') ?>" class="btn-create-new">
                    <i class="bi bi-plus-circle me-2"></i>新規ユーザー
                </a>
            </div>

            <?php if (!empty($users) && is_array($users)): ?>
                <div class="table-container">
                    <table class="table table-hover"> <?php // table-bordered table-striped を削除し、table-hover を追加 ?>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>氏名</th>
                                <th>ユーザー名</th>
                                <th>Eメール</th>
                                <th>有効</th>
                                <th>最終ログイン</th>
                                <th>グループ</th>
                                <th>操作</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                                <tr>
                                    <td><?= esc($user->id) ?></td>
                                    <td><?= esc($user->full_name ?? '未設定') ?></td>
                                    <td><?= esc($user->username ?? '') ?></td>
                                    <td><?= esc($user->getEmail() ?? '') ?></td>
                                    <td><?= $user->active ? '<span class="status-badge status-confirmed">有効</span>' : '<span class="status-badge status-pending">無効</span>' ?></td> <?php // status-badge クラスを適用 ?>
                                    <td><?= esc($user->last_active ? $user->last_active->format('Y-m-d H:i:s') : '') ?></td>
                                    <td>
                                        <?php
                                            // ★既存のUserエンティティのgetGroupsAsString()メソッドを使用（日本語対応済み）
                                            $groupsString = $user->getGroupsAsString();
                                            echo !empty($groupsString) ? esc($groupsString) : '';
                                        ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/users/edit/' . $user->id) ?>" class="btn-action btn-edit">編集</a>
                                        <?php if (auth()->id() !== $user->id) : // 自分自身は削除できないようにする ?>
                                        <a href="<?= site_url(route_to('admin.users.delete', $user->id)) ?>"
                                        class="btn-action btn-delete" <?php // 新しいクラスを適用 ?>
                                        onclick="return confirm('ユーザー「<?= esc($user->full_name ?: ($user->username ?: $user->getEmail() ?: 'ID:'.$user->id), 'js') ?>」を本当に削除しますか？\nこの操作は元に戻せません。');">
                                            削除
                                        </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <?php // ページネーションリンクの表示 ?>
                <?php if (isset($pager) && $pager->getPageCount() > 1) : ?>
                    <div class="pagination-container">
                        <?= $pager->links('default', 'default_full') ?>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <p>登録されているユーザーはいません。</p>
            <?php endif; ?>
        </div>
    </div>
<?= $this->endSection() ?>

<?php // このページ専用のCSSやJSを読み込む場合は、以下のセクションを使用します (今回は不要) ?>
<?php /*
<?= $this->section('page_specific_before_head_end') ?>
    <-- <link rel="stylesheet" href="..."> -->
<?= $this->endSection() ?>

<?= $this->section('page_specific_before_body_end') ?>
    <-- <script src="..."></script> -->
<?= $this->endSection() ?>
*/ ?>