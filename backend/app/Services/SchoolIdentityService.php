<?php

namespace App\Services;

use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use RuntimeException;

class SchoolIdentityService
{
    public function key(string $name): string
    {
        $key = preg_replace('/[^a-z0-9]+/', ' ', Str::ascii(mb_strtolower($name)));

        return Str::squish(preg_replace('/\b(huyen|thi xa|thanh pho)\b/', ' ', $key));
    }

    public function canonicalId(string $identifier): string
    {
        foreach (config('school_identity.merges', []) as $oldId => $alias) {
            if ($identifier === $oldId || $identifier === $alias['code']) {
                return $alias['target'];
            }
        }

        return $identifier;
    }

    public function findIn(Collection $schools, string $name, ?string $code = null): mixed
    {
        if (filled($code)) {
            $canonical = $this->canonicalId($code);
            $matches = $schools->filter(fn ($s) => $s->code === $code || $s->id === $canonical);
            // A reviewed target wins over its obsolete seed row before cleanup.
            $target = $matches->firstWhere('id', $canonical);
            if ($target) {
                return $target;
            }
            if ($matches->count() === 1) {
                return $matches->first();
            }
            if ($matches->count() > 1) {
                throw new RuntimeException('Mã cơ sở khớp nhiều bản ghi, cần đối chiếu: '.$code);
            }
        }
        $matches = $schools->filter(fn ($s) => $this->key($s->name) === $this->key($name));
        if ($matches->count() > 1) {
            throw new RuntimeException('Tên cơ sở khớp nhiều bản ghi, cần đối chiếu: '.$name);
        }

        return $matches->first();
    }
}
