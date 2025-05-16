<?php

namespace App\Models;

use CodeIgniter\Model;
use App\Entities\VehicleTypeEntity; // 作成したエンティティクラスをインポート

class VehicleTypeModel extends Model
{
    protected $table            = 'vehicle_types';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true; // DB定義で自動増分

    protected $returnType       = VehicleTypeEntity::class; // エンティティクラスを指定
    protected $useSoftDeletes   = true; // ソフトデリートを使用

    // insert()やupdate()で許可されるフィールド。
    // id, created_at, updated_at, deleted_at は自動的に処理されるか、
    // 直接のマスアサインメントから保護されるべきです。
    protected $allowedFields    = ['code', 'name', 'active'];

    // タイムスタンプを自動的に管理します
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at'; // DBのカラム名
    protected $updatedField     = 'updated_at'; // DBのカラム名
    protected $deletedField     = 'deleted_at'; // DBのカラム名 (ソフトデリート用)

    /**
     * タイムスタンプに使用する日付のフォーマット。
     * 'datetime' (Y-m-d H:i:s), 'date' (Y-m-d), 'int' (Unixタイムスタンプ)
     * DBのカラム型 DATETIME に合わせて 'datetime' を指定します。
     * @var string
     */
    protected $dateFormat       = 'datetime';

    // バリデーションルール
    protected $validationRules = [
        'code'   => [
            'label' => '車両種別コード', // エラーメッセージ表示用のラベル
            'rules' => 'required|string|exact_length[4]|is_unique[vehicle_types.code,id,{id}]',
            // 「数値4桁のコード」というDBコメントでしたが、VARCHAR型なので文字列として扱い、
            // 桁数とユニーク性をチェックします。数値のみに限定する場合は 'numeric' も追加できます。
            // 例: 'required|numeric|exact_length[4]|is_unique[vehicle_types.code,id,{id}]'
            // 初期データに '9999' があるため、ここでは 'string' としています。
        ],
        'name'   => [
            'label' => '車両種別名',
            'rules' => 'required|string|max_length[30]',
        ],
        'active' => [
            'label' => '有効フラグ',
            'rules' => 'required|in_list[0,1]',
        ],
    ];

    // バリデーションエラーメッセージ (任意でカスタマイズ)
    protected $validationMessages = [
        'code' => [
            'is_unique' => '入力された車両種別コードは既に使用されています。',
            'exact_length' => '{field}は4桁で入力してください。',
        ],
        'name' => [
            'required'   => '{field}は必須入力です。',
            'max_length' => '{field}は30文字以内で入力してください。',
        ],
    ];

    /**
     * バリデーションをスキップするかどうか。
     * 通常は false (バリデーションを実行)
     * @var bool
     */
    protected $skipValidation = false;

    // コールバック (例: codeを大文字に統一するなど)
    // protected $beforeInsert = ['strToUpperCode'];
    // protected $beforeUpdate = ['strToUpperCode'];
    //
    // protected function strToUpperCode(array $data)
    // {
    //     if (isset($data['data']['code'])) {
    //         $data['data']['code'] = strtoupper($data['data']['code']);
    //     }
    //     return $data;
    // }

    // --- カスタムメソッドの例 ---
    /**
     * 有効な車両種別のみを検索します。
     * @return array<VehicleTypeEntity>
     */
    public function findActive(): array
    {
        return $this->where('active', 1)->orderBy('id', 'ASC')->findAll(); // 表示順カラムがないためID順
    }
}