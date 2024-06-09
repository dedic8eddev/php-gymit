<?php declare(strict_types=1);

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Permissions_model
 *
 * Description: Model cares about users permissions (ACL)
 *
 * Tables:
 * - users_permissions
 * - groups_permission
 *
 * on __construct it loads all users permissions (from users groups and user specific permissions)
 *
 * Uses: Call defined public methods (hasReadPermission, hasCreatePermission, ...) with parameter : $section
 * Sections are defined aka constants in constants file
 *
 * Improvement: In the future can be this saved/loaded to te cache for speed improvement
 *
 */
class Permissions_model extends CI_Model
{
    private $permissions = [];

    public function __construct(){
        /*if(!$this->ion_auth->logged_in()){
            exit('Access forbidden.');
        }*/
        $this->gymdb->init();

        $this->permissions = $this->getAllUserPermissions((int) gym_userid());
    }

    /**
     * Returns array of all users permissions (from users groups + user specific permissions)
     *
     * result array is in format ['permissionName'] = ['read' => true, 'create' = true]
     *
     * @param int $userId
     * @return array
     */
    public function getAllUserPermissions(int $userId) {
        $users = $this->getUserPermissions($userId);
        $groups = $this->getUserGroupsPermissions($userId);

        $permissions = [];

        foreach(array_merge($groups, $users) as $permission) {
            $permissions[$permission['section']][$permission['action']] = true;
        }

        return $permissions;
    }

    /**
     * Returns array of all users permissions from table `users_permissions`
     *
     * @param int $userId
     * @return array
     */
    private function getUserPermissions(int $userId) : array {
        $userPermissions = $this->db
            ->select('permission_section AS section, permission_action AS action')
            ->from('users_permissions')
            ->where('user_id', $userId)
            ->group_by('section, action')
            ->get()
            ->result_array()
        ;

        if (empty($userPermissions)) {
            return [];
        }

        return $userPermissions;
    }

    /**
     * Returns array of all users groups permissions from table `groups_permissions`
     *
     * @param int $userId
     * @return array
     */
    private function getUserGroupsPermissions(int $userId) : array {
        $userGroupsPermissions = $this->db
            ->select('up.permission_section AS section, up.permission_action AS action')
            ->from('groups_permissions up')
            ->join('users_groups ug', 'ug.group_id=up.group_id', 'LEFT')
            ->where('ug.user_id', $userId)
            ->group_by('section, action')
            ->get()
            ->result_array()
        ;

        if (empty($userGroupsPermissions)) {
            return [];
        }

        return $userGroupsPermissions;
    }

    public function hasReadPermission(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_READ);
    }

    public function hasReadPermissionAtLeastInOneSection(array $sectionNames) : bool {
        $allowedSections = 0;

        foreach ($sectionNames as $sectionName) {
            if ($this->hasReadPermission($sectionName)) {
                $allowedSections++;
            }
        }

        return $allowedSections > 0;
    }

    public function hasReadPermissionForSectionDataOnly(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_READ_SECTION_DATA_ONLY);
    }

    public function hasCreatePermission(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_CREATE);
    }

    public function hasEditPermission(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_EDIT);
    }

    public function hasEditAndSendToApprovalPermission(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_EDIT_AND_SEND_TO_APPROVAL);
    }

    public function hasDeletePermission(string $sectionName) : bool {
        return $this->hasPermission($sectionName, ACTION_DELETE);
    }

    public function hasPermission(string $sectionName, string $action) : bool {
        return ! empty($this->permissions[$sectionName][$action]);
    }
}