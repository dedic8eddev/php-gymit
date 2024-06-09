<?php

if(! function_exists("hasReadPermission")) {
    /**
     * Method checks user permission to read section
     *
     * @uses hasReadPermission(); // checks read permission for current scope/controller section
     * @uses hasReadPermission(SECTION_CLIENTS); // checks read permission for given section (`clients`)
     *
     * @param string|null $sectionName
     * @return bool
     */
    function hasReadPermission(?string $sectionName = null) : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        if (empty($sectionName)) {
            $sectionName = $CI->sectionName();
        }

        return $CI->permissions->hasReadPermission($sectionName);
    }
}

if(! function_exists("hasReadPermissionAtLeastInOneSection")) {
    /**
     * Method checks user permission to read section
     *
     * @uses hasReadPermissionAtLeastInOneSection($sections); // checks read permission for given sections
     *
     * @param string|null $sectionName
     * @return bool
     */
    function hasReadPermissionAtLeastInOneSection(array $sections = []) : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        return $CI->permissions->hasReadPermissionAtLeastInOneSection($sections);
    }
}

if(! function_exists("hasCreatePermission")) {
    /**
     * Method checks user permission to create in section
     *
     * @uses hasCreatePermission(); // checks create permission for current scope/controller section
     * @uses hasCreatePermission(SECTION_CLIENTS); // checks create permission for given section (`clients`)
     *
     * @param string|null $sectionName
     * @return bool
     */
    function hasCreatePermission(?string $sectionName = null) : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        if (empty($sectionName)) {
            $sectionName = $CI->sectionName();
        }

        return $CI->permissions->hasCreatePermission($sectionName);
    }
}

if(! function_exists("hasEditPermission")) {
    function hasEditPermission() : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        return $CI->permissions->hasEditPermission($CI->sectionName());
    }
}

if(! function_exists("hasEditAndSendToApprovalPermission")) {
    function hasEditAndSendToApprovalPermission() : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        return $CI->permissions->hasEditAndSendToApprovalPermission($CI->sectionName());
    }
}

if(! function_exists("hasDeletePermission")) {
    function hasDeletePermission() : bool {
        /** @var Backend_Controller $CI */
        $CI =& get_instance();

        return $CI->permissions->hasDeletePermission($CI->sectionName());
    }
}