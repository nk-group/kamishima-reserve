<?php
namespace App\Models;
use CodeIgniter\Shield\Models\UserModel as ShieldUserModel;
use App\Entities\User;

class UserModel extends ShieldUserModel
{
    // $returnType をカスタムエンティティに設定
    protected $returnType = \App\Entities\User::class;

    // ★★★ 物理削除 ★★★
    protected $useSoftDeletes = false;
    

    // $allowedFields に 'full_name' を追加して、
    // save() メソッドなどで直接このフィールドを保存できるようにします
    protected $allowedFields = [
        'username',    // ShieldのUserModelから継承
        'active',      // ShieldのUserModelから継承
        'full_name',   // 新しく追加した氏名フィールド
        'last_active', // ShieldのUserModelから継承（必要に応じて）
        'status',      // ShieldのUserModelから継承（必要に応じて）
        'status_message', // ShieldのUserModelから継承（必要に応じて）
    ];
    
    // バリデーションルール
    protected $validationRules = [
        'full_name' => 'permit_empty|string|max_length[20]',
        'username'  => 'permit_empty|alpha_numeric_space|min_length[3]|max_length[30]|is_unique[users.username,id,{id}]',
        'active'    => 'permit_empty|in_list[0,1]',
    ];
    
    // バリデーションメッセージ
    protected $validationMessages = [
        'full_name' => [
            'max_length' => '氏名は20文字以内で入力してください。',
        ],
        'username' => [
            'alpha_numeric_space' => 'ユーザー名は英数字とスペースのみ使用できます。',
            'min_length' => 'ユーザー名は3文字以上である必要があります。',
            'max_length' => 'ユーザー名は30文字以内で入力してください。',
            'is_unique' => 'このユーザー名は既に使用されています。',
        ],
    ];
    
    /**
     * ユーザーをメールアドレスで検索します。
     *
     * @param string $email 検索するメールアドレス
     * @return User|null 見つかったユーザーまたはnull
     */
    public function findByEmail(string $email)
    {
        // まず auth_identities テーブルで該当するメールアドレスを検索
        $db = \Config\Database::connect();
        $builder = $db->table('auth_identities');
        $identity = $builder->where('type', 'email_password')
                            ->where('secret', $email)
                            ->get()
                            ->getRow();
        
        // 見つからなければ null を返す
        if (empty($identity)) {
            return null;
        }
        
        // 見つかったら、関連するユーザーを返す
        return $this->find($identity->user_id);
    }
    
    /**
     * 指定されたグループに属するユーザーを取得します。
     *
     * @param string $groupName グループ名
     * @return array ユーザーの配列
     */
    public function findByGroup(string $groupName)
    {
        return $this->join('auth_groups_users', 'auth_groups_users.user_id = users.id')
                    ->join('auth_groups', 'auth_groups.id = auth_groups_users.group_id')
                    ->where('auth_groups.name', $groupName)
                    ->findAll();
    }
    
    /**
     * ユーザーに関連するすべての情報を取得します（識別情報を含む）。
     *
     * @param int $userId ユーザーID
     * @return array|null ユーザー情報の連想配列またはnull
     */
    public function getUserWithIdentities(int $userId)
    {
        $user = $this->find($userId);
        
        if ($user === null) {
            return null;
        }
        
        // メールアドレスを取得
        $db = \Config\Database::connect();
        $identities = $db->table('auth_identities')
                        ->where('user_id', $userId)
                        ->get()
                        ->getResult();
        
        $result = [
            'user' => $user,
            'identities' => $identities,
        ];
        
        return $result;
    }
    
    /**
     * ユーザーが所属するグループのIDのリストを取得します。
     *
     * @param int $userId ユーザーID
     * @return array グループIDの配列
     */
    public function getUserGroupIds(int $userId): array
    {
        $db = \Config\Database::connect();
        $builder = $db->table('auth_groups_users');
        $query = $builder->select('group_id')
                        ->where('user_id', $userId)
                        ->get();
        
        $result = [];
        foreach ($query->getResult() as $row) {
            $result[] = $row->group_id;
        }
        
        return $result;
    }
}