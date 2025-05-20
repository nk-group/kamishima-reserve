<?= $this->extend('Layouts/admin-layout') ?>

<?= $this->section('page_title') ?>
    <?= esc($page_title ?? '車両種別マスタ') ?>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

    <h2><?= esc($page_title ?? '車両種別マスタ') ?></h2>

    <?= $this->include('Partials/_alert_messages') ?>

    <div class="actions mb-3">
        <a href="<?= route_to('admin.vehicle-types.new') ?>" class="btn btn-primary">新規車両種別登録</a>
    </div>

    <?php if (!empty($vehicle_types) && is_array($vehicle_types)): ?>
        <table class="table table-bordered table-striped">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>車両種別コード</th>
                    <th>車両種別名</th>
                    <th>有効</th>
                    <th>作成日時</th>
                    <th>更新日時</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vehicle_types as $type): ?>
                    <tr>
                        <td><?= esc($type->id) ?></td>
                        <td><?= esc($type->code) ?></td>
                        <td><?= esc($type->name) ?></td>
                        <td><?= $type->active ? '<span class="badge bg-success">有効</span>' : '<span class="badge bg-danger">無効</span>' ?></td>
                        <td><?= esc($type->created_at ? $type->created_at->format('Y-m-d H:i:s') : '') ?></td>
                        <td><?= esc($type->updated_at ? $type->updated_at->format('Y-m-d H:i:s') : '') ?></td>
                        <td>
                            <a href="<?= route_to('admin.vehicle-types.edit', $type->id) ?>" class="btn btn-sm btn-info">編集</a>
                            <form action="<?= route_to('admin.vehicle-types.delete', $type->id) ?>" method="post" class="d-inline" onsubmit="return confirm('車両種別「<?= esc($type->name, 'js') ?>」を本当に削除しますか？');">
                                <?= csrf_field() ?>
                                <input type="hidden" name="_method" value="POST"> <?php // ルートでPOST指定なので、そのままPOSTで良い ?>
                                <button type="submit" class="btn btn-sm btn-danger">削除</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>登録されている車両種別はありません。</p>
    <?php endif; ?>

<?= $this->endSection() ?>