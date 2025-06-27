<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * 予約ステータスマスタ (`reserve_statuses` テーブル) のモデルクラス。
 * 主にデータの読み取りと、ステータスコード/IDの相互変換に使用します。
 * このマスタデータは通常、シーダーによって管理されます。
 */
class ReserveStatusModel extends Model
{
    /**
     * 予約ステータスの定数: 未確定
     */
    public const STATUS_CODE_PENDING   = 'pending';
    /**
     * 予約ステータスの定数: 予約確定
     */
    public const STATUS_CODE_CONFIRMED = 'confirmed';
    /**
     * 予約ステータスの定数: 車検完了
     */
    public const STATUS_CODE_COMPLETED = 'completed';
    /**
     * 予約ステータスの定数: キャンセル
     */
    public const STATUS_CODE_CANCELED = 'canceled';

    /**
     * 対応するデータベーステーブル名
     * @var string
     */
    protected $table = 'reserve_statuses';

    /**
     * テーブルの主キー
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * 主キーが自動増分ではないことを示す
     * (シーダーでIDを直接指定するため)
     * @var bool
     */
    protected $useAutoIncrement = false;

    /**
     * モデルが返すデータの型
     * @var string
     */
    protected $returnType = \App\Entities\ReserveStatusEntity::class;

    /**
     * ソフトデリートを使用しない
     * @var bool
     */
    protected $useSoftDeletes = false;

    /**
     * フィールド保護 (読み取り専用マスタなので、書き込みは想定しない)
     * @var bool
     */
    protected $protectFields = true; // true のままで問題なし

    /**
     * 書き込みを許可するフィールド (読み取り専用なので空配列でも可、
     * あるいはシーダーでのみ使用することを明示)
     * @var string[]
     */
    protected $allowedFields = ['id', 'code', 'name', 'description', 'sort_order']; // シーダーでの利用を想定

    /**
     * タイムスタンプカラムは使用しない
     * @var bool
     */
    protected $useTimestamps = false;

    // バリデーションルールやメッセージは、このモデル経由で
    // 書き込みを行わないのであれば不要です。
    // protected $validationRules      = [];
    // protected $validationMessages   = [];
    // protected $skipValidation       = true; // 書き込みしないなら true でも良い

    // --- カスタム読み取り系メソッド ---

    /**
     * 全ての予約ステータスをソート順で取得し、
     * フォームのセレクトボックスなどで使いやすい形式の配列で返します。
     *
     * @return array<int, string> [id => name] の形式の連想配列
     */
    public function getListForForm(): array
    {
        $statuses = $this->orderBy('sort_order', 'ASC')
                         ->orderBy('name', 'ASC')
                         ->findAll();

        $list = [];
        if ($this->returnType === 'array') {
            foreach ($statuses as $status) {
                if (isset($status['id']) && isset($status['name'])) {
                    $list[(int)$status['id']] = $status['name']; // IDをキーに
                }
            }
        } elseif ($this->returnType === 'object' || class_exists($this->returnType)) {
            foreach ($statuses as $status) {
                if (isset($status->id) && isset($status->name)) {
                    $list[(int)$status->id] = $status->name;
                }
            }
        }
        return $list;
    }

    /**
     * 指定されたIDに対応するステータス名を取得します。
     *
     * @param int $id ステータスID
     * @return string|null ステータス名。見つからない場合はnull。
     */
    public function getNameById(int $id): ?string
    {
        $status = $this->find($id);
        if ($status) {
            return $this->returnType === 'array' ? ($status['name'] ?? null) : ($status->name ?? null);
        }
        return null;
    }

    /**
     * 指定されたステータスコードに対応するステータスIDを取得します。
     *
     * @param string $code ステータスコード
     * @return int|null ステータスID。見つからない場合はnull。
     */
    public function getIdByCode(string $code): ?int
    {
        $status = $this->where('code', $code)->first();
        if ($status) {
            $id = $this->returnType === 'array' ? ($status['id'] ?? null) : ($status->id ?? null);
            return $id !== null ? (int)$id : null;
        }
        return null;
    }

    /**
     * 指定されたステータスコードに対応するステータス名を取得します。
     *
     * @param string $code ステータスコード
     * @return string|null ステータス名。見つからない場合はnull。
     */
    public function getNameByCode(string $code): ?string
    {
        $status = $this->where('code', $code)->first();
        if ($status) {
            return $this->returnType === 'array' ? ($status['name'] ?? null) : ($status->name ?? null);
        }
        return null;
    }

    /**
     * 全てのステータスレコードを、コードをキーとした連想配列で取得します。
     * 例: ['pending' => ['id' => 1, 'name' => '未確定', ...], ...]
     *
     * @return array<string, array>
     */
    public function getAllStatusesByCode(): array
    {
        $statuses = $this->orderBy('sort_order', 'ASC')->findAll();
        $listByCode = [];
        $keyName = 'code'; // codeカラムをキーにする

        if ($this->returnType === 'array') {
            foreach ($statuses as $status) {
                if (isset($status[$keyName])) {
                    $listByCode[$status[$keyName]] = $status;
                }
            }
        } elseif ($this->returnType === 'object' || class_exists($this->returnType)) {
            foreach ($statuses as $status) {
                if (isset($status->{$keyName})) {
                    $listByCode[$status->{$keyName}] = (array)$status; // オブジェクトも配列に変換して格納
                }
            }
        }
        return $listByCode;
    }
}