<?php

declare(strict_types=1);

/**
 * Schema.org JSON-LD: MedicalClinic, Physician, PostalAddress,
 * OpeningHoursSpecification.
 */
final class Schema
{
    public static function medicalClinic(string $name, string $description): string
    {
        $data = [
            '@context' => 'https://schema.org',
            '@type' => 'MedicalClinic',
            'name' => $name,
            'description' => $description,
            'url' => App::url() . '/',
            'telephone' => Setting::get('phone_intl', '+4953535511'),
            'medicalSpecialty' => 'Pediatric',
            'address' => [
                '@type' => 'PostalAddress',
                'streetAddress' => Setting::get('street', 'Westernstraße 31'),
                'postalCode' => '38154',
                'addressLocality' => 'Königslutter am Elm',
                'addressCountry' => 'DE',
            ],
            'openingHoursSpecification' => array_map(static function (array $spec): array {
                return [
                    '@type' => 'OpeningHoursSpecification',
                    'dayOfWeek' => $spec['dayOfWeek'],
                    'opens' => $spec['opens'],
                    'closes' => $spec['closes'],
                ];
            }, OpeningHours::schemaSpecs()),
        ];

        $physicians = [];
        $doctors = [];
        if (Database::tryConn() !== null) {
            try {
                $doctors = TeamMember::doctors();
            } catch (Throwable $e) {
                $doctors = [];
            }
        }
        foreach ($doctors as $doctor) {
            $physicians[] = [
                '@type' => 'Physician',
                'name' => $doctor['name'],
                'medicalSpecialty' => 'Pediatric',
                'jobTitle' => $doctor['role'],
                'worksFor' => ['@type' => 'MedicalClinic', 'name' => $name],
            ];
        }
        if ($physicians !== []) {
            $data['employee'] = $physicians;
        }

        return json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
