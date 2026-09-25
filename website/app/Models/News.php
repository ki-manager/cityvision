<?php

declare(strict_types=1);

/**
 * News-Modell (Aktuelles aus der Praxis).
 */
final class News
{
    public static function active(int $limit = 20, ?string $slug = null): ?array
    {
        if ($slug !== null) {
            $row = Database::fetch(
                'SELECT * FROM news WHERE slug = ? AND active = 1
                 AND publish_date <= CURDATE()
                 AND (start_date IS NULL OR start_date <= CURDATE())
                 AND (end_date IS NULL OR end_date >= CURDATE())',
                [$slug]
            );
            return $row ?: null;
        }
        return Database::fetchAll(
            'SELECT * FROM news WHERE active = 1
             AND publish_date <= CURDATE()
             AND (start_date IS NULL OR start_date <= CURDATE())
             AND (end_date IS NULL OR end_date >= CURDATE())
             ORDER BY important DESC, publish_date DESC LIMIT ' . max(1, $limit)
        );
    }

    public static function important(): array
    {
        return Database::fetchAll(
            'SELECT * FROM news WHERE active = 1 AND important = 1
             AND publish_date <= CURDATE()
             AND (start_date IS NULL OR start_date <= CURDATE())
             AND (end_date IS NULL OR end_date >= CURDATE())
             ORDER BY publish_date DESC LIMIT 3'
        );
    }

    public static function all(): array
    {
        return Database::fetchAll('SELECT * FROM news ORDER BY publish_date DESC, id DESC');
    }

    public static function find(int $id): ?array
    {
        return Database::fetch('SELECT * FROM news WHERE id = ?', [$id]);
    }

    public static function save(array $data, ?int $id = null): int
    {
        $fields = [
            'title' => $data['title'],
            'slug' => $data['slug'],
            'summary' => $data['summary'] ?? '',
            'content' => $data['content'] ?? '',
            'publish_date' => $data['publish_date'],
            'start_date' => $data['start_date'] ?: null,
            'end_date' => $data['end_date'] ?: null,
            'important' => (int) ($data['important'] ?? 0),
            'active' => (int) ($data['active'] ?? 1),
        ];
        if ($id === null) {
            $cols = implode(', ', array_keys($fields));
            $marks = implode(', ', array_fill(0, count($fields), '?'));
            Database::execute("INSERT INTO news ($cols) VALUES ($marks)", array_values($fields));
            return (int) Database::lastInsertId();
        }
        $sets = implode(', ', array_map(fn ($c) => "$c = ?", array_keys($fields)));
        $values = array_values($fields);
        $values[] = $id;
        Database::execute("UPDATE news SET $sets WHERE id = ?", $values);
        return $id;
    }

    public static function delete(int $id): void
    {
        Database::execute('DELETE FROM news WHERE id = ?', [$id]);
    }
}
