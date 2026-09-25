<?php

declare(strict_types=1);

/**
 * Service-Modell (Leistungen).
 */
final class Service
{
    public const GROUPS = [
        'vorsorge' => 'Vorsorge',
        'diagnostik' => 'Diagnostik',
        'chronisch' => 'Chronische Erkrankungen',
        'impfungen' => 'Impfungen',
        'beratung' => 'Beratung',
    ];

    public static function active(): array
    {
        return Database::fetchAll('SELECT * FROM services WHERE active = 1 ORDER BY sort_order, id');
    }

    public static function byGroup(): array
    {
        $grouped = array_fill_keys(array_keys(self::GROUPS), []);
        foreach (self::active() as $service) {
            $grouped[$service['group_key']][] = $service;
        }
        return array_filter($grouped);
    }

    public static function homepage(): array
    {
        return Database::fetchAll(
            'SELECT * FROM services WHERE active = 1 AND homepage = 1 ORDER BY sort_order LIMIT 6'
        );
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM services WHERE id = ?', [$id]);
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = [
            'slug' => $data['slug'],
            'group_key' => $data['group_key'] ?? 'allgemein',
            'title' => $data['title'],
            'description' => $data['description'] ?? '',
            'icon' => $data['icon'] ?? 'stethoscope',
            'sort_order' => (int) ($data['sort_order'] ?? 0),
            'active' => (int) ($data['active'] ?? 1),
        ];
        if ($id === null) {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO services ($cols) VALUES ($marks)", array_values($fields));
            return (int) Database::lastInsertId();
        }
        $sets = implode(', ', array_map(fn ($c) => "$c = ?", array_keys($fields)));
        $values = array_values($fields);
        $values[] = $id;
        Database::execute("UPDATE services SET $sets WHERE id = ?", $values);
        return $id;
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM services WHERE id = ?', [$id]);
    }
}
