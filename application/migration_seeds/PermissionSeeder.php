<?php

/** Just for constants */
define('BASEPATH', '');
require_once __DIR__ . '/../config/constants.php';

use Phinx\Seed\AbstractSeed;

final class PermissionSeeder extends AbstractSeed
{
    public function getDependencies()
    {
        return ['GroupSeeder'];
    }

    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $this->query('SET FOREIGN_KEY_CHECKS = 0');
        $this->query('TRUNCATE groups_permissions');

        $this->insertPermissions();

        $this->query('SET FOREIGN_KEY_CHECKS = 1');
    }

    private function insertPermissions()
    {

        $this->createGroupPermissions(ADMINISTRATOR, [
            SECTION_GYMS                    => self::allPermissions(),
            SECTION_SITE_SETTINGS           => self::allPermissions(),
            SECTION_CARD_MANAGEMENT         => self::allPermissions(),
            SECTION_CASH_REGISTER           => self::allPermissions(),
            SECTION_CUSTOM_FIELDS           => self::allPermissions(),
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => self::allPermissions(),
            SECTION_USERS                   => self::allPermissions(),
            SECTION_COACHES                 => self::allPermissions(),
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => self::allPermissions(),
            SECTION_DEPOT                   => self::allPermissions(),
            SECTION_PRICE_LIST              => self::allPermissions(),
            SECTION_MEMBERSHIP              => self::allPermissions(),
            SECTION_PAYMENTS                => self::allPermissions(),
            SECTION_INVOICES                => self::allPermissions(),
            SECTION_TRANSACTIONS            => self::allPermissions(),
            SECTION_REPORTING               => self::allPermissions(),
            SECTION_VOUCHERS                => self::allPermissions(),
            SECTION_CMS                     => self::allPermissions(),
        ]);

        $this->createGroupPermissions(STORE_MANAGER, [
            SECTION_GYMS                    => self::allPermissions(),
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => self::allPermissions(),
            SECTION_CASH_REGISTER           => self::allPermissions(),
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => self::allPermissions(),
            SECTION_USERS                   => self::allPermissions(),
            SECTION_COACHES                 => self::allPermissions(),
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => self::allPermissions(),
            SECTION_DEPOT                   => self::allPermissions(),
            SECTION_PRICE_LIST              => self::allPermissions(),
            SECTION_MEMBERSHIP              => self::allPermissions(),
            SECTION_PAYMENTS                => self::allPermissions(),
            SECTION_INVOICES                => self::allPermissions(),
            SECTION_TRANSACTIONS            => self::allPermissions(),
            SECTION_REPORTING               => self::allPermissions(),
            SECTION_VOUCHERS                => self::allPermissions(),
            SECTION_CMS                     => self::allPermissions(),
        ]);

        $this->createGroupPermissions(SENIOR_RECEPTIONIST, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => self::allPermissions(),
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => self::allPermissions(),
            SECTION_USERS                   => [ACTION_READ,],
            SECTION_COACHES                 => [ACTION_READ,],
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [ACTION_READ,],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => self::allPermissions(),
            SECTION_INVOICES                => self::allPermissions(),
            SECTION_TRANSACTIONS            => self::allPermissions(),
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => self::allPermissions(),
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(RECEPTIONIST, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => self::allPermissions(),
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => self::allPermissions(),
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [ACTION_READ,],
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [ACTION_READ,],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => self::allPermissions(),
            SECTION_INVOICES                => [ACTION_READ,],
            SECTION_TRANSACTIONS            => [ACTION_READ,],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => self::allPermissions(),
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(WELLNESS_SERVICE, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => [ACTION_READ,],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [ACTION_READ,],
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [ACTION_READ,],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(CHILDREN_PLAY_AREA_WORKER, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => [],
            SECTION_LOCKERS                 => [],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [],
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [],
            SECTION_MEMBERSHIP              => [],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(WEBMASTER, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => [],
            SECTION_LOCKERS                 => [],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [],
            SECTION_LESSONS                 => [ACTION_READ, ACTION_CREATE, ACTION_EDIT],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [],
            SECTION_MEMBERSHIP              => [],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => self::allPermissions(),
        ]);

        $this->createGroupPermissions(GYM_AND_STUDIO_MANAGER, [
            SECTION_GYMS                    => self::allPermissions(),
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => self::allPermissions(),
            SECTION_CASH_REGISTER           => self::allPermissions(),
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => self::allPermissions(),
            SECTION_USERS                   => [ACTION_READ,],
            SECTION_COACHES                 => self::allPermissions(),
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => self::allPermissions(),
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => self::allPermissions(),
            SECTION_INVOICES                => self::allPermissions(),
            SECTION_TRANSACTIONS            => self::allPermissions(),
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => self::allPermissions(),
        ]);

        $this->createGroupPermissions(MASTER_TRAINER, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => [],
            SECTION_USERS                   => [ACTION_READ,],
            SECTION_COACHES                 => [ACTION_READ,],
            SECTION_CLIENTS                 => self::allPermissions(),
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(PERSONAL_TRAINER, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => self::allPermissions(),
            SECTION_LOCKERS                 => [],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [],
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [ACTION_READ,],
            SECTION_MEMBERSHIP              => [ACTION_READ,],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(INSTRUCTOR, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => [],
            SECTION_LOCKERS                 => [],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [],
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [],
            SECTION_MEMBERSHIP              => [],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);

        $this->createGroupPermissions(SERVICE_TECHNICIAN, [
            SECTION_GYMS                    => [],
            SECTION_SITE_SETTINGS           => [],
            SECTION_CARD_MANAGEMENT         => [],
            SECTION_CASH_REGISTER           => [],
            SECTION_CUSTOM_FIELDS           => [],
            SECTION_DASHBOARDS              => [ACTION_READ,],
            SECTION_LOCKERS                 => [ACTION_READ,],
            SECTION_USERS                   => [],
            SECTION_COACHES                 => [],
            SECTION_CLIENTS                 => [],
            SECTION_LESSONS                 => [ACTION_READ,],
            SECTION_DEPOT                   => [],
            SECTION_PRICE_LIST              => [],
            SECTION_MEMBERSHIP              => [],
            SECTION_PAYMENTS                => [],
            SECTION_INVOICES                => [],
            SECTION_TRANSACTIONS            => [],
            SECTION_REPORTING               => [],
            SECTION_VOUCHERS                => [],
            SECTION_CMS                     => [],
        ]);
    }

    private function createGroupPermissions(int $groupId, array $sections)
    {
        $groupPermissionData = [];

        foreach ($sections as $section => $actions) {
            $groupPermissionData = array_merge($groupPermissionData, self::createSection($groupId, $section, $actions));
        }

        $this->saveGroupPermissions($groupPermissionData);
    }

    private function saveGroupPermissions(array $groupPermissionData): void
    {
        $this
            ->table('groups_permissions')
            ->insert($groupPermissionData)
            ->save()
        ;
    }

    private static function createSection(int $groupId, string $section, array $actions): array
    {
        $permissions = [];

        foreach ($actions as $action) {
            $permissions[] = [
                'id'                    => null,
                'group_id'              => $groupId,
                'permission_section'    => $section,
                'permission_action'     => $action,
            ];
        }

        return $permissions;
    }

    private static function allPermissions(): array
    {
        return [ACTION_READ, ACTION_CREATE, ACTION_DELETE, ACTION_EDIT,];
    }
}
