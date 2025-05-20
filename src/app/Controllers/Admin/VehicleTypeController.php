<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController; // 正しいBaseControllerを継承
use App\Models\VehicleTypeModel;
use App\Entities\VehicleTypeEntity;
use CodeIgniter\HTTP\RedirectResponse;

class VehicleTypeController extends BaseController
{
    protected VehicleTypeModel $vehicleTypeModel;

    public function __construct()
    {
        $this->vehicleTypeModel = model(VehicleTypeModel::class);
    }

    /**
     * 車両種別一覧を表示します。
     *
     * @return string レンダリングされたHTML文字列
     */
    public function index(): string
    {
        // ソフトデリートされていない車両種別をID昇順で取得
        $vehicleTypes = $this->vehicleTypeModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'page_title'    => '車両種別マスタ',
            'vehicle_types' => $vehicleTypes,
        ];
        return $this->render('Admin/VehicleTypes/index', $data);
    }

    /**
     * 新規車両種別登録フォームを表示します。
     *
     * @return string レンダリングされたHTML文字列
     */
    public function new(): string
    {
        $data = [
            'page_title' => '新規車両種別登録',
            'errors'     => session()->getFlashdata('errors'), // バリデーションエラーがあればセット
        ];
        return $this->render('Admin/VehicleTypes/new', $data);
    }

    /**
     * 新規車両種別を作成します。
     *
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function create(): RedirectResponse
    {
        $vehicleType = new VehicleTypeEntity($this->request->getPost());

        if ($this->vehicleTypeModel->save($vehicleType)) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('message', '車両種別が正常に登録されました。');
        }

        // 保存に失敗した場合
        return redirect()->back()->withInput()->with('errors', $this->vehicleTypeModel->errors());
    }

    /**
     * 指定されたIDの車両種別編集フォームを表示します。
     *
     * @param int|null $id 車両種別ID
     * @return string|RedirectResponse レンダリングされたHTML文字列またはリダイレクト
     */
    public function edit(int $id = null)
    {
        if ($id === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '車両種別IDが指定されていません。');
        }

        $vehicleType = $this->vehicleTypeModel->find($id);

        if ($vehicleType === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '指定されたIDの車両種別が見つかりません。');
        }

        $data = [
            'page_title'   => '車両種別編集',
            'vehicle_type' => $vehicleType,
            'errors'       => session()->getFlashdata('errors'),
        ];
        return $this->render('Admin/VehicleTypes/edit', $data);
    }

    /**
     * 指定されたIDの車両種別情報を更新します。
     *
     * @param int|null $id 車両種別ID
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function update(int $id = null): RedirectResponse
    {
        if ($id === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '車両種別IDが指定されていません。');
        }

        $vehicleType = $this->vehicleTypeModel->find($id);
        if ($vehicleType === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '指定されたIDの車両種別が見つかりません。');
        }

        // POSTされたデータでエンティティを更新
        $postData = $this->request->getPost();
        // activeフィールドがPOSTデータにない場合（チェックボックスがオフの場合）は 0 を設定
        $postData['active'] = $postData['active'] ?? '0';

        if ($this->vehicleTypeModel->update($id, $postData)) {
             return redirect()->to(route_to('admin.vehicle-types.edit', $id))
                             ->with('message', '車両種別情報が正常に更新されました。');
        }

        // 更新に失敗した場合
        return redirect()->back()->withInput()->with('errors', $this->vehicleTypeModel->errors());
    }

    /**
     * 指定されたIDの車両種別を削除します（ソフトデリート）。
     *
     * @param int|null $id 削除対象の車両種別ID
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function delete(int $id = null): RedirectResponse
    {
        if ($id === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '車両種別IDが指定されていません。');
        }

        $vehicleType = $this->vehicleTypeModel->find($id);
        if ($vehicleType === null) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('error', '削除対象の車両種別が見つかりませんでした。');
        }

        if ($this->vehicleTypeModel->delete($id)) {
            return redirect()->to(route_to('admin.vehicle-types.index'))
                             ->with('message', '車両種別「' . esc($vehicleType->name) . '」を削除しました。');
        }

        return redirect()->to(route_to('admin.vehicle-types.index'))
                         ->with('error', '車両種別の削除に失敗しました。エラー: ' . implode(' ', $this->vehicleTypeModel->errors() ?: []));
    }
}