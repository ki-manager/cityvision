<?php

declare(strict_types=1);

/**
 * Team-Modell.
 */
final class TeamMember
{
    public static function active(): array
    {
        return Database::fetchAll('SELECT * FROM team WHERE active = 1 ORDER BY sort_order, id');
    }

    public static function doctors(): array
    {
        return Database::fetchAll(
            "SELECT * FROM team WHERE active = 1 AND role_type = 'arzt' ORDER BY sort_order, id"
        );
    }

    public static function staff(): array
    {
        return Database::fetchAll(
            "SELECT * FROM team WHERE active = 1 AND role_type = 'mitarbeitende' ORDER BY sort_order, id"
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM team WHERE id = ?', [$id]);
    }

    public static function findBySlug(string $slug): ?array
    {
        return Database::fetch('SELECT * FROM team WHERE slug = ? AND active = 1', [$slug]);
    }

    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM team ORDER BY sort_order, id');
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = [
            'slug' => $data['slug'],
            'name' => $data['name'],
            'role' => $data['role'] ?? '',
            'role_type' => $data['role_type'] ?? 'mitarbeitende',
            'specialty' => $data['specialty'] ?? '',
            'qualifications' => $data['qualifications'] ?? null,
            'bio' => $data['bio'] ?? null,
            'photo' => $data['photo'] ?? '',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => (int) ($data['active'] ?? 1),
        ];
        if ($id === null) {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO team ($cols) VALUES ($marks)", array_values($fields));
            return (int) Database::lastInsertId();
        }
        $sets = implode(', ', array_map(fn ($c) => "$c = ?", array_keys($fields)));
        $values = array_values($fields);
        $values[] = $id;
        Database::execute("UPDATE team SET $sets WHERE id = ?", $values);
        return $id;
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM team WHERE id = ?', [$id]);
    }
}
