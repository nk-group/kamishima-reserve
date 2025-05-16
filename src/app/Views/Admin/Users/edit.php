<?= $this->extend('Layouts/admin-layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('page_title') ?>
    <?= esc($page_title ?? 'ユーザー編集') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <h2><?= esc($page_title ?? 'ユーザー編集') ?></h2>

    <?= $this->include('Partials/_alert_messages') // ★ メッセージ表示用のパーシャルビューをインクルード ?>

    <?php
        // フォームの送信先ルート
        // $user->id はコントローラから渡された編集対象ユーザーのID
    ?>
    <form action="<?= site_url('admin/users/update/' . $user->id) ?>" method="post" class="needs-validation" novalidate>
        <?= csrf_field() // CSRF対策トークン ?>
        <input type="hidden" name="_method" value="POST"> <?php // HTMLフォームはPUTを直接サポートしないため、POSTで送信し、ルート側でPUT/PATCHとして扱うか、CI4のフォームスプーフィングを利用。今回はPOSTのままでコントローラ側で処理 ?>

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

        <hr class="my-4">

        <button class="btn btn-primary btn-lg" type="submit">更新する</button>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary btn-lg">キャンセル</a>
    </form>

<?= $this->endSection() ?>


<?= $this->section('page_specific_before_body_end') // ページ固有のJSセクション ?>
    <script>
        // Bootstrap 5 のクライアントサイドバリデーション表示用スクリプト
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
<?= $this->endSection() ?>