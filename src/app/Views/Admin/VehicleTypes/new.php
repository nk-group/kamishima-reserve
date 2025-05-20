<?= $this->extend('Layouts/admin-layout') ?>

<?= $this->section('page_title') ?>
    <?= esc($page_title ?? '新規車両種別登録') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <h2><?= esc($page_title ?? '新規車両種別登録') ?></h2>

    <?= $this->include('Partials/_alert_messages') ?>

    <form action="<?= route_to('admin.vehicle-types.create') ?>" method="post" class="needs-validation" novalidate>
        <?= csrf_field() ?>

        <div class="mb-3">
            <label for="code" class="form-label">車両種別コード <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= isset($errors['code']) ? 'is-invalid' : '' ?>" id="code" name="code" value="<?= old('code', '') ?>" required maxlength="4">
            <?php if (isset($errors['code'])): ?>
                <div class="invalid-feedback">
                    <?= esc($errors['code']) ?>
                </div>
            <?php else: ?>
                <div class="invalid-feedback">
                    車両種別コードは必須です (4桁)。
                </div>
            <?php endif; ?>
            <small class="form-text text-muted">例: 0001, 9999 (4桁の数値または文字列)</small>
        </div>

        <div class="mb-3">
            <label for="name" class="form-label">車両種別名 <span class="text-danger">*</span></label>
            <input type="text" class="form-control <?= isset($errors['name']) ? 'is-invalid' : '' ?>" id="name" name="name" value="<?= old('name', '') ?>" required maxlength="30">
            <?php if (isset($errors['name'])): ?>
                <div class="invalid-feedback">
                    <?= esc($errors['name']) ?>
                </div>
            <?php else: ?>
                 <div class="invalid-feedback">
                    車両種別名は必須です (30文字以内)。
                </div>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" id="active" name="active" <?= old('active', '1') === '1' ? 'checked' : '' ?>>
                <label class="form-check-label" for="active">
                    有効にする
                </label>
            </div>
        </div>

        <hr class="my-4">

        <button class="btn btn-primary btn-lg" type="submit">登録する</button>
        <a href="<?= route_to('admin.vehicle-types.index') ?>" class="btn btn-secondary btn-lg">キャンセル</a>
    </form>

<?= $this->endSection() ?>

<?= $this->section('page_specific_before_body_end') ?>
    <script>
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