<?= $this->extend('Layouts/admin-layout') // 管理者向け共通レイアウトを継承 ?>

<?= $this->section('page_title') // レイアウトの $page_title に値を設定 ?>
    <?= esc($page_title ?? '新規ユーザー登録') ?>
<?= $this->endSection() ?>

<?= $this->section('content') // レイアウトのメインコンテンツ部分 ?>

    <h2><?= esc($page_title ?? '新規ユーザー登録') ?></h2>

    <?= $this->include('Partials/_alert_messages') // ★ メッセージ表示用のパーシャルビューをインクルード ?>

    <form action="<?= site_url('admin/users/create') ?>" method="post" class="needs-validation" novalidate>
        <?= csrf_field() // CSRF対策トークン ?>

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

        <hr class="my-4">

        <button class="btn btn-primary btn-lg" type="submit">登録する</button>
        <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary btn-lg">キャンセル</a>
    </form>

<?= $this->endSection() ?>


<?= $this->section('page_specific_before_body_end') // ページ固有のJSセクション ?>
    <script>
        // Bootstrap 5 のクライアントサイドバリデーション表示用スクリプト
        (function () {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
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