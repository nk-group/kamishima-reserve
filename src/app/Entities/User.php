<?php

namespace App\Entities;

use CodeIgniter\Shield\Entities\User as ShieldUser;

class User extends ShieldUser
{
    /**
     * 氏名を取得します。
     *
     * @return string
     */
    public function getFullName(): ?string
    {
        return $this->attributes['full_name'] ?? null;
    }

    /**
     * 氏名を設定します。
     *
     * @param string $fullName
     * @return $this
     */
    public function setFullName(?string $fullName): self
    {
        $this->attributes['full_name'] = $fullName;
        
        return $this;
    }
    
    /**
     * ユーザーのアクティブ状態を文字列として取得します。
     * 
     * @return string 'アクティブ' または '非アクティブ'
     */
    public function getActiveStatus(): string
    {
        return $this->active ? 'アクティブ' : '非アクティブ';
    }
    
    /**
     * ユーザーの全グループ名を取得します。カンマ区切りの文字列として返します。
     * 
     * @return string カンマ区切りのグループ名
     */
    public function getGroupsAsString(): string
    {
        $groups = $this->getGroups();
        
        if (empty($groups)) {
            return '';
        }
        
        // グループ名のみを抽出
        $groupNames = [];
        foreach ($groups as $group) {
            $groupConfig = config('AuthGroups')->groups[$group] ?? null;
            $groupNames[] = $groupConfig['title'] ?? $group;
        }
        
        return implode(', ', $groupNames);
    }

    /**
     * ユーザーのメイングループ（最初のグループ）の日本語名を取得します。
     * 
     * @return string メイングループの日本語名
     */
    public function getPrimaryGroupJapaneseName(): string
    {
        $groups = $this->getGroups();
        
        if (empty($groups)) {
            return '未設定';
        }
        
        $authGroupsConfig = config('AuthGroups');
        $primaryGroup = $groups[0];
        
        return $authGroupsConfig->groups[$primaryGroup]['title'] ?? $primaryGroup;
    }
}