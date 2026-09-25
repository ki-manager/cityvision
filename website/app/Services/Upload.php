<?php

declare(strict_types=1);

/**
 * Sicherer Bild-Upload: MIME/Endung/Format prüfen, Zufallsname,
 * Ablage in public/uploads/ (PHP dort per .htaccess gesperrt).
 */
final class Upload
{
    private const ALLOWED_MIME = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
        'image/avif' => 'avif',
    ];

    public static function image(array $file): array
    {
        if (($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) {
            return ['ok' => false, 'error' => 'Upload fehlgeschlagen.'];
        }
        $size = (int) ($file['size'] ?? 0);
        $max = (int) (config('app')['upload_max_bytes'] ?? 4194304);
        if ($size <= 0 || $size > $max) {
            return ['ok' => false, 'error' => 'Datei zu groß (max. ' . round($max / 1048576, 1) . ' MB).'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = (string) $finfo->file($file['tmp_name']);
        if (!isset(self::ALLOWED_MIME[$mime])) {
            return ['ok' => false, 'error' => 'Nur JPG, PNG, WebP oder AVIF erlaubt.'];
        }

        if (!@getimagesize($file['tmp_name'])) {
            return ['ok' => false, 'error' => 'Datei ist kein gültiges Bild.'];
        }

        $ext = self::ALLOWED_MIME[$mime];
        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        $target = ROOT_PATH . '/public/uploads/' . $filename;

        $dir = dirname($target);
        if (!is_dir($dir)) {
            return ['ok' => false, 'error' => 'Upload-Verzeichnis existiert nicht.'];
        }
        if (!is_writable($dir)) {
            return ['ok' => false, 'error' => 'Upload-Verzeichnis ist nicht beschreibbar.'];
        }
        if (!move_uploaded_file($file['tmp_name'], $target)) {
            return ['ok' => false, 'error' => 'Datei konnte nicht gespeichert werden.'];
        }

        try {
            Database::execute(
                'INSERT INTO media (filename, original_name, mime_type, size) VALUES (?, ?, ?, ?)',
                [$filename, (string) ($file['name'] ?? ''), $mime, $size]
            );
        } catch (Throwable $e) {
        }

        return ['ok' => true, 'filename' => $filename];
    }
}
