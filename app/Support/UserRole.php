<?php

namespace App\Support;

final class UserRole
{
    public const SUPER_ADMIN = 'super_admin';

    public const ADMIN_KONTEN = 'admin_konten';

    public const PIMPINAN = 'pimpinan';

    public const ANGGOTA_POKDARWIS = 'anggota_pokdarwis';

    public const PETUGAS_LAPANGAN = 'petugas_lapangan';

    /**
     * Get all valid user roles.
     *
     * @return list<string>
     */
    public static function all(): array
    {
        return [
            self::SUPER_ADMIN,
            self::ADMIN_KONTEN,
            self::PIMPINAN,
            self::ANGGOTA_POKDARWIS,
            self::PETUGAS_LAPANGAN,
        ];
    }
}