<?= $this->extend('Layouts/admin-layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('page_title') // レイアウトの $page_title に値を設定 ?>
    <?= esc($page_title ?? 'ユーザーマスタ') ?>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>

    <h2><?= esc($page_title ?? 'ユーザーマスタ') ?></h2>

    <div class="actions mb-3">
        <a href="<?= site_url('admin/users/new') // 新規作成ページへのリンク (後で作成) ?>" class="btn btn-primary">新規ユーザー登録</a>
    </div>

    <?php if (!empty($users) && is_array($users)): ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
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
                        <td><?= esc($user->full_name ?? '未設定') // 追加した氏名カラム ?></td>
                        <td><?= esc($user->username ?? '') // username はShieldのデフォルトでは必須ではない ?></td>
                        <td><?= esc($user->getEmail() ?? '') ?></td>
                        <td><?= $user->active ? '<span class="badge bg-success">有効</span>' : '<span class="badge bg-danger">無効</span>' ?></td>
                        <td><?= esc($user->last_active ? $user->last_active->format('Y-m-d H:i:s') : '') ?></td>
                        <td>
                            <?php
                                $groups = $user->getGroups();
                                echo esc(implode(', ', $groups));
                            ?>
                        </td>
                        <td>
                            <a href="<?= site_url('admin/users/edit/' . $user->id) // 編集ページへのリンク (後で作成) ?>" class="btn btn-sm btn-info">編集</a>
                            <?php if (auth()->id() !== $user->id) : // 自分自身は削除できないようにする ?>
                            <a href="<?= site_url(route_to('admin.users.delete', $user->id)) // 名前付きルートを使用 ?>"
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('ユーザー「<?= esc($user->full_name ?: ($user->username ?: $user->getEmail() ?: 'ID:'.$user->id), 'js') ?>」を本当に削除しますか？\nこの操作は元に戻せません。');">
                                削除
                            </a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されているユーザーはいません。</p>
    <?php endif; ?>

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