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

    /**
     * Get user role options
     * with human-friendly labels.
     *
     * @return array<string, string>
     */
    public static function values(): array
    {
        return [
            self::SUPER_ADMIN => 'Super Admin',
            self::ADMIN_KONTEN => 'Admin Konten',
            self::PIMPINAN => 'Pimpinan',
            self::ANGGOTA_POKDARWIS => 'Anggota Pokdarwis',
            self::PETUGAS_LAPANGAN => 'Petugas Lapangan',
        ];
    }

    /**
     * Get role label.
     */
    public static function label(string $role): string
    {
        return self::values()[$role] ?? $role;
    }
}