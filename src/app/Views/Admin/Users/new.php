<?= $this->extend('Layouts/admin_layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('title') // レイアウトの <title> タグに値を設定 ?>
    <?= esc($page_title ?? '新規ユーザー登録') ?>
<?= $this->endSection() ?>

<?= $this->section('page_header_content') // ページヘッダー用セクション ?>
    <div class="page-header">
        <h1 class="page-title">
            <i class="bi bi-person-plus-fill"></i> <?php // 新規ユーザー登録に合うアイコンを追加 ?>
            <?= esc($h1_title) ?>
        </h1>
    </div>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>
    <div class="page-content">
        <?= $this->include('Partials/_alert_messages') // ★ メッセージ表示用のパーシャルビューをインクルード ?>

        <form action="<?= site_url('admin/users/create') ?>" method="post" class="needs-validation" novalidate>
            <?= csrf_field() // CSRF対策トークン ?>
            <?php // <input type="hidden" name="_method" value="POST"> は不要なため削除 (new.phpには元々存在しないが念のため) ?>

            <div class="mb-3">
                <label for="full_name" class="form-label">氏名 <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="full_name" name="full_name" value="<?= old('full_name', '') // 再入力時の値保持 ?>" required>
                <div class="invalid-feedback">
                    氏名は必須です。
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Eメールアドレス <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" value="<?= old('email', '') ?>" required>
                <div class="invalid-feedback">
                    有効なEメールアドレスを入力してください。
                </div>
            </div>

            <div class="mb-3">
                <label for="username" class="form-label">ユーザー名 (任意)</label>
                <input type="text" class="form-control" id="username" name="username" value="<?= old('username', '') ?>">
                <small class="form-text text-muted">ログインには使用しません。表示名として任意で設定できます。</small>
            </div>

            <div class="mb-3">
                <label for="password" class="form-label">パスワード <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password" name="password" required>
                <div class="invalid-feedback">
                    パスワードは必須です。
                </div>
            </div>

            <div class="mb-3">
                <label for="password_confirm" class="form-label">パスワード（確認用） <span class="text-danger">*</span></label>
                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                <div class="invalid-feedback">
                    確認用パスワードを入力してください。
                </div>
            </div>

            <div class="mb-3">
                <label for="groups" class="form-label">所属グループ <span class="text-danger">*</span></label>
                <select class="form-select" id="groups" name="groups[]" multiple required size="3">
                    <?php
                        // Config/AuthGroups.php で定義されているグループを取得して表示
                        $authGroupsConfig = config('AuthGroups');
                        $availableGroups = [];
                        if (isset($authGroupsConfig->groups)) {
                            foreach ($authGroupsConfig->groups as $groupName => $groupDetails) {
                                // 'user' グループは通常管理者画面からは割り当てないことが多いので例として除外
                                // if ($groupName === 'user') continue;
                                $availableGroups[$groupName] = $groupDetails['title'] ?? ucfirst($groupName);
                            }
                        }
                        // コントローラから $groups を渡す場合はそれを優先する設計も可能
                        // $groupsForForm = $groups ?? $availableGroups;
                        $groupsForForm = $availableGroups; // 今回は直接Configから取得
                    ?>
                    <?php foreach ($groupsForForm as $groupName => $groupTitle): ?>
                        <option value="<?= esc($groupName, 'attr') ?>" <?= (is_array(old('groups')) && in_array($groupName, old('groups'))) ? 'selected' : '' ?>>
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
                    <input class="form-check-input" type="checkbox" value="1" id="active" name="active" <?= old('active', '1') === '1' ? 'checked' : '' ?>>
                    <label class="form-check-label" for="active">
                        アカウントを有効にする
                    </label>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="action-buttons action-buttons-right"> <?php // 新規作成画面ではタイムスタンプ情報がないため、ボタンを右寄せにする専用クラスを追加 ?>
                <?php // timestamp-info は新規作成時には不要なので削除 ?>
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
