<?php

namespace App\Support;

use Illuminate\Support\Str;

final class SkillList
{
    /** @return array<int, string> */
    public static function parse(mixed $value): array
    {
        $items = is_array($value)
            ? $value
            : (preg_split('/[,;\r\n]+/u', (string) $value) ?: []);

        $result = [];
        $seen = [];

        foreach ($items as $item) {
            $name = Str::of((string) $item)->squish()->trim()->toString();
            if ($name === '') {
                continue;
            }

            $key = self::normalize($name);
            if ($key === '' || isset($seen[$key])) {
                continue;
            }

            $seen[$key] = true;
            $result[] = $name;

        }

        return $result;
    }

    public static function toText(mixed $value): string
    {
        return implode(PHP_EOL, self::parse($value));
    }

    public static function normalize(string $value): string
    {
        return Str::of($value)->ascii()->lower()->squish()->trim()->toString();
    }
}
