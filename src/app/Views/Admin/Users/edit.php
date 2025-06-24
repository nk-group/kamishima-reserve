<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? 'ユーザー編集') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') // ページヘッダー用セクション ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-person-fill"></i> <?php // ユーザー編集に合うアイコンを追加 ?>
            <?= esc($h1_title) ?>
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') // ★ メッセージ表示用のパーシャルビューをインクルード ?>

        <?php
            // フォームの送信先ルート
            // $user->id はコントローラから渡された編集対象ユーザーのID
        ?>
        <form action="<?= site_url('admin/users/update/' . $user->id) ?>" method="post" class="needs-validation" novalidate>
            <?= csrf_field() // CSRF対策トークン ?>
            <?php // <input type="hidden" name="_method" value="POST"> は不要なため削除 ?>

            <div class="mb-3">
                <label for="full_name" class="form-label">氏名 <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', esc($user->full_name ?? '')) ?>" required>
                <div class="invalid-feedback">
                    氏名は必須です。
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Eメールアドレス <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email', esc($user_email ?? '')) // コントローラから渡された $user_email を使用 ?>" required>
                <div class="invalid-feedback">
                    有効なEメールアドレスを入力してください。
                </div>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">ユーザー名 (任意)</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= old('username', esc($user->username ?? '')) ?>">
                <small class="form-text text-muted">ログインには使用しません。表示名として任意で設定できます。</small>
            </div>

            <hr class="my-3">
            <p class="text-muted">パスワードを変更する場合のみ入力してください。</p>

            <div class="mb-3">
                <label for="password" class="form-label">新しいパスワード</label>
                <input type="password" class="form-control" id="password" name="password">
                <div class="invalid-feedback">
                    パスワードの形式が正しくありません。
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirm" class="form-label">新しいパスワード（確認用）</label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm">
                <div class="invalid-feedback">
                    確認用パスワードを入力してください。
                </div>
            </div>

            <hr class="my-3">

            <div class="mb-3">
                <label for="groups" class="form-label">所属グループ <span class="text-danger">*</span></label>
                <select class="form-select" id="groups" name="groups[]" multiple required size="3">
                    <?php foreach ($available_groups as $groupName => $groupTitle): ?>
                        <option value="<?= esc($groupName, 'attr') ?>" <?= (is_array(old('groups', $user_groups)) && in_array($groupName, old('groups', $user_groups))) ? 'selected' : '' ?>>
                            <?= esc($groupTitle) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="form-text text-muted">Ctrlキー（またはMacではCmdキー）を押しながらクリックすると複数選択できます。</small>
                <div class="invalid-feedback">
                    少なくとも1つのグループを選択してください。
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="1" id="active" name="active" <?= old('active', $user->active) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">
                        アカウントを有効にする
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <div class="timestamp-info">
                    <div>新規登録：<?= esc($user->created_at ? $user->created_at->format('Y年n月j日 H:i') : 'N/A') ?></div>
                    <div>最終更新：<?= esc($user->updated_at ? $user->updated_at->format('Y年n月j日 H:i') : 'N/A') ?></div>
                </div>
                <div class="d-flex gap-3">
                    <button class="btn-primary-custom" type="submit"><i class="bi bi-check-circle me-2"></i>保存</button>
                    <a href="<?= site_url('admin/users') ?>" class="btn-outline-custom"><i class="bi bi-x-circle me-2"></i>キャンセル</a>
                </div>
            </div>
        </form>
    </div>
<?= $this->endSection() ?>

<?php // クライアントサイドバリデーションスクリプトは共通JSファイルに移動するため削除 ?>
<?= $this->section('page_specific_scripts') ?>
    <?php // スクリプトは assets/js/admin/utils/form-validation.js に移動 ?>
<?= $this->endSection() ?>
