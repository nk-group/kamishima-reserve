<?php

namespace App\Controllers\Admin;

use App\Controllers\Admin\BaseController;
use App\Models\UserModel;
use App\Entities\User;
use CodeIgniter\Shield\Models\UserIdentityModel;
use CodeIgniter\HTTP\RedirectResponse;


class UserController extends BaseController
{

    /**
     * ユーザー一覧を表示します。
     *
     * @return string レンダリングされたHTML文字列
     */
    public function index(): string
    {
        $userModel = auth()->getProvider();
        $users = $userModel->orderBy('id', 'ASC')->findAll();

        $data = [
            'page_title' => 'ユーザーマスタ | 車検予約管理システム',
            'body_id'    => 'page-admin-users-index',
            'users'      => $users,
        ];
        return $this->render('Admin/Users/index', $data);
    }


    /**
     * 新規ユーザー登録フォームを表示します。
     *
     * @return string レンダリングされたHTML文字列
     */
    public function new(): string
    {
        $data = [
            'page_title'       => '新規ユーザー登録 | 車検予約管理システム',
            'body_id'          => 'page-admin-users-new',
            'available_groups' => $this->getAvailableGroups(),
        ];
        return $this->render('Admin/Users/new', $data);
    }


    /**
     * 新規ユーザーを作成（フォームからのPOSTリクエストを処理）します。
     *
     * @return RedirectResponse リダイレクト
     */
    public function create(): RedirectResponse
    {
        // バリデーションルールを設定
        $rules = [
            'full_name'        => 'required|string|max_length[20]',
            'email'            => 'required|valid_email|is_unique[auth_identities.secret]',
            'username'         => 'permit_empty|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]',
            'password'         => 'required|strong_password[]',
            'password_confirm' => 'required|matches[password]',
            'groups'           => 'required|is_array',
            'active'           => 'permit_empty|in_list[0,1]',
        ];        
        
        // バリデーションエラーメッセージのカスタマイズ
        $messages = [
            'full_name' => [
                'required'   => '氏名は必須項目です。',
                'max_length' => '氏名は20文字以内で入力してください。',
            ],
            'email' => [
                'required'    => 'メールアドレスは必須項目です。',
                'valid_email' => '有効なメールアドレスを入力してください。',
                'is_unique'   => 'このメールアドレスは既に登録されています。',
            ],
            'username' => [
                'alpha_numeric_punct' => 'ユーザー名は英数字と一部の記号のみ使用できます。',
                'min_length'          => 'ユーザー名は3文字以上で入力してください。',
                'max_length'          => 'ユーザー名は30文字以内で入力してください。',
                'is_unique'           => 'このユーザー名は既に使用されています。',
            ],
            'password' => [
                'required'        => 'パスワードは必須項目です。',
                'strong_password' => 'パスワードは、大文字、小文字、数字、記号をそれぞれ1文字以上含み、8文字以上である必要があります。', // 例
            ],
            'password_confirm' => [
                'required' => '確認用パスワードは必須項目です。',
                'matches'  => 'パスワードと確認用パスワードが一致しません。',
            ],
            'groups' => [
                'required' => '少なくとも1つのグループを選択してください。',
                'is_array' => 'グループの指定が正しくありません。',
            ],
        ];

        // バリデーション実行
        if (! $this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // トランザクション開始
        $db = \Config\Database::connect();
        $db->transStart();
        
        try {

            // UserModelのインスタンスを取得 - Shieldの標準的な方法
            $userModel = auth()->getProvider();
            $user = new User([
                'username' => $this->request->getPost('username') ?: null,
                'full_name' => $this->request->getPost('full_name'),
                'email' => $this->request->getPost('email'),
                'password' => $this->request->getPost('password'),
                'active' => (bool)$this->request->getPost('active'),
            ]);
            
            // ユーザー情報を保存
            if (! $userModel->save($user)) {
                log_message('error', '[UserController::create] Initial user save failed. Errors: ' . json_encode($userModel->errors()));
                throw new \Exception('ユーザー基本情報の保存に失敗しました。' . implode(' ', $userModel->errors() ?: []));
            }            

            // 保存したユーザーのIDを取得
            $userId = $userModel->getInsertID();
            $user->id = $userId;            

            // グループへの割り当て
            $groups = $this->request->getPost('groups');
            if (is_array($groups)) {
                foreach ($groups as $group) {
                    $user->addGroup(strtolower(esc($group, 'attr')));
                }
            }
                        
            // トランザクション終了
            $db->transComplete();
            if ($db->transStatus() === false) {
                // 何らかの理由でトランザクションが失敗した場合
                log_message('error', '[UserController::create] Transaction failed after attempting to complete.');
                throw new \RuntimeException('データベース処理中にエラーが発生しました。');
            }

            // 成功メッセージをフラッシュデータとして設定
            $displayName = esc($user->full_name ?: $user->username ?: $user->email);
            session()->setFlashdata('message', 'ユーザー「' . $displayName . '」が正常に登録されました。');

            return redirect()->to(site_url('admin/users'));

        } catch (\Throwable $e) { // Exception および Error を捕捉
            // トランザクションロールバック
            $db->transRollback();

            // 詳細なエラー情報をログに記録
            $logData = [
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'request'   => [ // パスワードは記録しない
                    'full_name' => $this->request->getPost('full_name'),
                    'email'     => $this->request->getPost('email'),
                    'username'  => $this->request->getPost('username'),
                    'groups'    => $this->request->getPost('groups'),
                    'active'    => $this->request->getPost('active'),
                ]
            ];
            log_message('error', '[UserController::create] User registration exception: ' . json_encode($logData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[UserController::create] Stack trace: ' . $e->getTraceAsString());
            }

            // ユーザーに表示するエラーメッセージ (汎用的で安全なもの)
            $userErrorMessage = 'ユーザー登録処理中に予期せぬエラーが発生しました。しばらく経ってから再度お試しいただくか、管理者にお問い合わせください。';
            if (ENVIRONMENT === 'development') { // 開発環境では詳細なエラーを見せることも可
                // $userErrorMessage .= ' (詳細: ' . esc($e->getMessage()) . ')';
            }
            // エラーメッセージと入力値を保持してフォームにリダイレクト
            return redirect()->back()->withInput()->with('error', $userErrorMessage);
        }
    }


    /**
     * 指定したIDのユーザー情報編集フォームを表示します。
     *
     * @param int|null $id ユーザーID
     * @return string|RedirectResponse レンダリングされたHTML文字列またはリダイレクト
     */
    public function edit($id = null)
    {
        if ($id === null) {
            return redirect()->to('admin/users')->with('error', 'ユーザーIDが指定されていません。');
        }

        $userModel = auth()->getProvider();
        $user = $userModel->find($id);

        if ($user === null) {
            return redirect()->to('admin/users')->with('error', '指定されたIDのユーザーが見つかりません。');
        }

        // ユーザーのEmailを取得
        $identityModel = model(UserIdentityModel::class);
        $identity = $identityModel->where('user_id', $id)
                                  ->where('type', 'email_password')
                                  ->first();
        
        // ユーザーが所属するグループを取得
        $userGroups = $user->getGroups();

        $data = [
            'page_title'       => 'ユーザー編集 | 車検予約管理システム',
            'body_id'          => 'page-admin-users-edit',
            'user'             => $user,
            'user_email'       => $identity ? $identity->secret : '',
            'user_groups'      => $userGroups,
            'available_groups' => $this->getAvailableGroups(),
        ];

        return $this->render('Admin/Users/edit', $data);
    }




    /**
     * 指定したIDのユーザー情報を更新します。
     *
     * @param int|null $id ユーザーID
     * @return RedirectResponse リダイレクト
     */
    public function update($id = null): RedirectResponse
    {
        if ($id === null) {
            return redirect()->to(site_url('admin/users'))->with('error', 'ユーザーIDが指定されていません。');
        }

        // ユーザーの存在確認
        $userModel = auth()->getProvider();
        $user = $userModel->findById($id);
        if ($user === null) {
            return redirect()->to(site_url('admin/users'))->with('error', '指定されたIDのユーザーが見つかりません。');
        }

        // バリデーションルールを設定
        $rules = [
            'full_name' => 'required|string|max_length[20]',
            'email'     => 'required|valid_email',
            'username'  => "permit_empty|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username,id,{$id}]",
            'groups'    => 'required|is_array',
            'active'    => 'permit_empty|in_list[0,1]',
        ];
        // パスワードが入力された場合のみ、パスワードのバリデーションを追加
        $newPassword = $this->request->getPost('password');
        if (!empty($newPassword)) {
            $rules['password'] = 'strong_password[]';
            $rules['password_confirm'] = 'required|matches[password]'; // passwordが入力されたら確認も必須
        }

        // メールアドレスの一意性チェック（Shield の標準機能を使って最適化）
        $email = $this->request->getPost('email');
        $currentEmail = $user->getEmail(); // 現在のメールアドレスを取得
        
        if ($email !== $currentEmail) {
            // メールアドレスが変更された場合のみ一意性チェックを追加
            $rules['email'] = 'required|valid_email|is_unique[auth_identities.secret]';
        }

        // バリデーションエラーメッセージのカスタマイズ (createメソッドと同様のものを想定)
        $messages = [
            'full_name' => [
                'required' => '氏名は必須項目です。',
                'max_length' => '氏名は20文字以内で入力してください。'
            ],
            'email' => [
                'required' => 'メールアドレスは必須項目です。',
                'valid_email' => '有効なメールアドレスを入力してください。',
                'is_unique' => 'このメールアドレスは既に他のユーザーが使用しています。',
            ],
            'username' => [
                'alpha_numeric_punct' => 'ユーザー名は英数字と一部の記号のみ使用できます。',
                'min_length[3]' => 'ユーザー名は3文字以上である必要があります。',
                'max_length' => 'ユーザー名は30文字以内で入力してください。',
                'is_unique' => 'このユーザー名は既に使用されています。'
            ],
            'password' => [
                'strong_password' => 'パスワードは十分に強力なものを設定してください。'
            ],
            'password_confirm' => [
                'required' => '確認用パスワードは必須項目です。',
                'matches' => 'パスワードと確認用パスワードが一致しません。'
            ],
            'groups' => [
                'required' => '少なくとも1つのグループを選択してください。',
                'is_array' => 'グループの指定が正しくありません。'
            ]
        ];

        // バリデーション実行
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $validatedData = $this->validator->getValidated();

        // トランザクション開始
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // 基本情報の更新
            $user->fill([
                'username'  => !empty($validatedData['username']) ? $validatedData['username'] : $user->username,
                'full_name' => $validatedData['full_name'],
                'active'    => $this->request->getPost('active') !== null ? (bool)$this->request->getPost('active') : $user->active,
            ]);

            // メールとパスワードの更新
            $user->email = $validatedData['email'];
            if (!empty($newPassword)) {
                $user->password = $newPassword;
            }

            // 保存処理
            if (!$userModel->save($user)) {
                log_message('error', '[UserController::update] UserModel save failed for ID ' . $id . '. Errors: ' . json_encode($userModel->errors()));
                throw new \RuntimeException('ユーザー情報の保存に失敗しました。' . implode(' ', $userModel->errors() ?: []));
            }

            // グループ割り当ての更新
            $groups = $this->request->getPost('groups') ?? [];
            if (is_array($groups) && count($groups) > 0) {
                // 配列要素を安全にサニタイズしてからsyncGroupsに渡す
                $sanitizedGroups = array_map(fn($group) => strtolower(esc($group, 'attr')), $groups);
                $user->syncGroups(...$sanitizedGroups);
            } else {
                // 全てのグループ選択が解除された場合
                $user->syncGroups();
            }


            // トランザクション終了
            $db->transComplete();
            if ($db->transStatus() === false) {
                throw new \Exception('データベースの更新処理中にエラーが発生しました。');
            }

            session()->setFlashdata('message', 'ユーザー情報が正常に更新されました。');
            return redirect()->to(site_url('admin/users/edit/' . $id)); // 更新後は編集画面にリダイレクト
            //return redirect()->to(site_url('admin/users'));

        } catch (\Throwable $e) {
            
            $db->transRollback();

            // エラーロギング (パスワードは含めない)
            $errorData = [
                'message'   => $e->getMessage(),
                'code'      => $e->getCode(),
                'file'      => $e->getFile(),
                'line'      => $e->getLine(),
                'request_data' => [
                    'id'        => $id,
                    'full_name' => $this->request->getPost('full_name'),
                    'email'     => $this->request->getPost('email'), // バリデーション前のemail
                    'username'  => $this->request->getPost('username'),
                    'groups'    => $this->request->getPost('groups'),
                    'active'    => $this->request->getPost('active'),
                ]
            ];
            log_message('error', '[UserController::update] User update exception for ID ' . $id . ': ' . json_encode($errorData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            if (ENVIRONMENT === 'development') {
                log_message('debug', '[UserController::update] Stack trace: ' . $e->getTraceAsString());
            }

            // ユーザー向けの一般的なエラーメッセージ
            $userErrorMessage = 'ユーザー情報の更新中に予期せぬエラーが発生しました。しばらく経ってから再度お試しいただくか、管理者にお問い合わせください。';
            if (ENVIRONMENT === 'development') {
                // 開発環境では、より詳細な情報を見せることも検討できますが、セキュリティには注意が必要です。
                // $userErrorMessage .= ' (詳細: ' . esc($e->getMessage()) . ')';
            }
            // エラーメッセージと入力値を保持してフォームにリダイレクト
            return redirect()->back()->withInput()->with('error', $userErrorMessage);
        }
    }



    /**
     * 指定されたIDのユーザーを削除します。
     *
     * @param int $id 削除対象のユーザーID
     * @return RedirectResponse リダイレクトレスポンス
     */
    public function delete(int $id): RedirectResponse
    {
        $userModel = auth()->getProvider();

        // 削除対象のユーザーが存在するか確認
        // findById() は App\Entities\User インスタンスまたは null を返す
        $user = $userModel->findById($id);

        if ($user === null) {
            return redirect()->to(site_url('admin/users'))
                             ->with('error', '指定されたユーザーが見つかりませんでした。ID: ' . $id);
        }

        // 自分自身のアカウントを削除できないようにする制御
        if ($user->id === auth()->id()) {
            return redirect()->to(site_url('admin/users'))
                             ->with('error', '自分自身のアカウントを削除することはできません。');
        }

        // トランザクション開始 (Shieldのdeleteが関連テーブルも操作するため推奨)
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Shield標準のdelete機能を使用
            if (! $userModel->delete($id)) {
                log_message('error', '[UserController::delete] Failed to delete user ID: ' . $id . '. Model errors: ' . json_encode($userModel->errors()));
                throw new \RuntimeException('ユーザーの削除に失敗しました。');
            }

            $db->transComplete();
            if ($db->transStatus() === false) {
                log_message('error', '[UserController::delete] Transaction failed for user ID: ' . $id);
                throw new \Exception('データベース処理中にエラーが発生し、ユーザー削除を完了できませんでした。');
            }

            $userNameForMessage = esc($user->full_name ?: ($user->username ?: $user->getEmail() ?: 'ID:'.$id));
            session()->setFlashdata('message', 'ユーザー「' . $userNameForMessage . '」を削除しました。');
            return redirect()->to(site_url('admin/users'));

        } catch (\Throwable $e) {
            $db->transRollback();

            log_message('error', '[UserController::delete] User deletion exception for ID ' . $id . ': ' . $e->getMessage() . "\n" . $e->getTraceAsString());
            $userErrorMessage = 'ユーザー削除中に予期せぬエラーが発生しました。管理者にお問い合わせください。';
            if (ENVIRONMENT === 'development') {
                // $userErrorMessage .= ' (エラー詳細: ' . esc($e->getMessage()) . ')';
            }
            return redirect()->to(site_url('admin/users'))->with('error', $userErrorMessage);
        }
    }



    /**
     * 利用可能なグループのリストを取得します。
     *
     * @return array グループ名 => 表示名 の連想配列
     */
    private function getAvailableGroups(): array
    {
         // Shield の AuthGroups 設定からグループを取得
        $groups = config('AuthGroups')->groups;
        $availableGroups = [];
        
        foreach ($groups as $name => $group) {
            $availableGroups[$name] = $group['title'] ?? $name;
        }
        
        return $availableGroups;
    }

}