<?php

namespace App\Services;

use App\Models\DemandProposal;
use App\Models\Employer;
use App\Models\ReportingPeriod;
use App\Models\School;
use App\Models\Skill;
use App\Models\TrainingCapacity;
use App\Models\TrainingLevel;
use App\Models\TrainingMajor;
use App\Models\WorkforceDemand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class AppsScriptPortalImportService
{
    /**
     * @param array<string, mixed> $payload
     * @return array{employers:int,demands:int,capacities:int,proposals:int,skipped:int,errors:array<int,string>}
     */
    public function import(array $payload, bool $includeSamples = false, bool $publish = false, bool $dryRun = false): array
    {
        $payload = $this->unwrapPayload($payload);
        $employers = is_array($payload['employers'] ?? null) ? $payload['employers'] : [];
        $listings = is_array($payload['listings'] ?? null) ? $payload['listings'] : [];
        $proposals = is_array($payload['proposals'] ?? null) ? $payload['proposals'] : [];
        $result = ['employers' => 0, 'demands' => 0, 'capacities' => 0, 'proposals' => 0, 'skipped' => 0, 'errors' => []];

        DB::beginTransaction();

        try {
            foreach ($employers as $index => $employer) {
                if (! is_array($employer)) {
                    $result['errors'][] = 'Doanh nghiệp dòng '.($index + 1).' không đúng cấu trúc.';
                    continue;
                }

                try {
                    $this->importEmployer($employer);
                    $result['employers']++;
                } catch (Throwable $exception) {
                    $result['errors'][] = $this->recordError('Doanh nghiệp', $employer, $exception);
                }
            }

            foreach ($listings as $index => $listing) {
                if (! is_array($listing)) {
                    $result['errors'][] = 'Bản ghi cung/cầu dòng '.($index + 1).' không đúng cấu trúc.';
                    continue;
                }

                if (! $includeSamples && $this->isSample($listing)) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    $kind = (string) ($listing['kind'] ?? '');
                    if ($kind === 'demand') {
                        $this->importDemand($listing, $publish);
                        $result['demands']++;
                    } elseif ($kind === 'capacity') {
                        $this->importCapacity($listing, $publish);
                        $result['capacities']++;
                    } else {
                        throw new \RuntimeException('thiếu loại demand/capacity');
                    }
                } catch (Throwable $exception) {
                    $result['errors'][] = $this->recordError('Cung/cầu', $listing, $exception);
                }
            }

            foreach ($proposals as $index => $proposal) {
                if (! is_array($proposal)) {
                    $result['errors'][] = 'Đề xuất dòng '.($index + 1).' không đúng cấu trúc.';
                    continue;
                }

                if (! $includeSamples && $this->isSample($proposal)) {
                    $result['skipped']++;
                    continue;
                }

                try {
                    $this->importProposal($proposal, $publish);
                    $result['proposals']++;
                } catch (Throwable $exception) {
                    $result['errors'][] = $this->recordError('Đề xuất', $proposal, $exception);
                }
            }

            if ($dryRun) {
                DB::rollBack();
            } else {
                DB::commit();
            }
        } catch (Throwable $exception) {
            DB::rollBack();
            throw $exception;
        }

        return $result;
    }

    /** @param array<string, mixed> $record */
    private function importEmployer(array $record): Employer
    {
        $name = trim((string) ($record['name'] ?? ''));
        if (mb_strlen($name) < 3) {
            throw new \RuntimeException('thiếu tên doanh nghiệp');
        }

        $id = trim((string) ($record['id'] ?? ''));
        $externalId = $id === '' ? null : Str::limit('apps-script-employer:'.$id, 100, '');
        $existing = $externalId ? Employer::query()->where('external_id', $externalId)->first() : null;
        $existing ??= Employer::query()->get()->first(
            fn (Employer $item): bool => $this->normalize($item->name) === $this->normalize($name),
        );
        $employer = $existing ?? new Employer();

        $employer->fill([
            'name' => Str::limit($name, 200, ''),
            'aliases' => is_array($record['aliases'] ?? null) ? array_values($record['aliases']) : null,
            'industry' => $this->nullableText($record['sector'] ?? null, 150),
            'region' => $this->nullableText($record['region'] ?? null, 150),
            'employee_count' => $this->nullablePositiveInteger($record['workers'] ?? null),
            // sourceCode from the prototype is retained separately because its
            // own notes state that it is not always a verified tax code.
            'source_code' => $this->nullableText($record['sourceCode'] ?? null, 50),
            'address' => $this->nullableText($record['location'] ?? null, 255),
            'scope' => $this->nullableText($record['scope'] ?? null, 10000),
            'data_note' => $this->nullableText($record['note'] ?? null, 10000),
            'source_details' => is_array($record['sources'] ?? null) ? $record['sources'] : null,
            'reviewed_at' => $this->nullableDate($record['reviewedAt'] ?? null),
            'status' => 'ACTIVE',
            'is_verified' => false,
            'source' => 'APPS_SCRIPT',
            'external_id' => $externalId,
        ]);
        $employer->save();

        return $employer;
    }

    /** @param array<string, mixed> $listing */
    private function importDemand(array $listing, bool $publish): void
    {
        $externalId = $this->externalId($listing);
        $employer = $this->resolveEmployer((string) ($listing['org'] ?? ''));
        $major = $this->resolveMajor((string) ($listing['profession'] ?? ''));
        $level = $this->resolveLevel((string) ($listing['level'] ?? ''));
        $period = $this->resolvePeriod((string) ($listing['period'] ?? ''));
        $quantity = $this->quantity($listing);

        $demand = WorkforceDemand::query()->updateOrCreate(
            ['external_id' => $externalId],
            [
                'employer_id' => $employer->id,
                'training_major_id' => $major->id,
                'education_level_id' => $level->id,
                'reporting_period_id' => $period->id,
                'purpose' => $this->purpose((string) ($listing['purpose'] ?? '')),
                'quantity' => $quantity,
                'location' => $this->nullableText($listing['district'] ?? null, 200),
                'age_range' => $this->nullableText($listing['age'] ?? null, 80),
                'salary' => $this->nullableText($listing['salary'] ?? null, 100),
                'note' => $this->nullableText($listing['note'] ?? null, 2000),
                'status' => $publish ? 'PUBLISHED' : 'DRAFT',
                'source' => 'APPS_SCRIPT',
                'is_sample' => $this->isSample($listing),
            ],
        );

        $demand->skills()->sync($this->resolveSkillIds($listing['skills'] ?? []));
    }

    /** @param array<string, mixed> $listing */
    private function importCapacity(array $listing, bool $publish): void
    {
        $externalId = $this->externalId($listing);
        $school = $this->resolveSchool((string) ($listing['org'] ?? ''));
        $major = $this->resolveMajor((string) ($listing['profession'] ?? ''));
        $level = $this->resolveLevel((string) ($listing['level'] ?? ''));
        $period = $this->resolvePeriod((string) ($listing['period'] ?? ''));

        $capacity = TrainingCapacity::query()->updateOrCreate(
            [
                'school_id' => $school->id,
                'training_major_id' => $major->id,
                'education_level_id' => $level->id,
                'reporting_period_id' => $period->id,
            ],
            [
                'quantity' => $this->quantity($listing),
                'location' => $this->nullableText($listing['location'] ?? null, 255),
                'duration' => $this->nullableText($listing['duration'] ?? null, 120),
                'equipment' => $this->nullableText($listing['equipment'] ?? null, 2000),
                'note' => $this->nullableText($listing['note'] ?? null, 2000),
                'status' => $publish ? 'ACTIVE' : 'INACTIVE',
                'source' => 'APPS_SCRIPT',
                'external_id' => $externalId,
                'is_sample' => $this->isSample($listing),
            ],
        );

        $capacity->skills()->sync($this->resolveSkillIds($listing['skills'] ?? []));
    }

    /** @param array<string, mixed> $proposal */
    private function importProposal(array $proposal, bool $publish): void
    {
        $demandId = $this->prefixedExternalId((string) ($proposal['demandId'] ?? ''));
        $capacityId = $this->prefixedExternalId((string) ($proposal['capacityId'] ?? ''));
        $demand = WorkforceDemand::query()->where('external_id', $demandId)->first();
        $capacity = TrainingCapacity::query()->where('external_id', $capacityId)->first();

        if (! $demand || ! $capacity) {
            throw new \RuntimeException('không tìm thấy nhu cầu hoặc năng lực tương ứng');
        }

        if ((int) $demand->training_major_id !== (int) $capacity->training_major_id
            || (string) $demand->education_level_id !== (string) $capacity->education_level_id
            || (int) $demand->reporting_period_id !== (int) $capacity->reporting_period_id) {
            throw new \RuntimeException('nhu cầu và năng lực không cùng nghề, trình độ hoặc kỳ');
        }

        $plan = trim((string) ($proposal['plan'] ?? ''));
        if (mb_strlen($plan) < 10) {
            throw new \RuntimeException('phương án phải có ít nhất 10 ký tự');
        }

        DemandProposal::query()->updateOrCreate(
            ['external_id' => $this->externalId($proposal)],
            [
                'workforce_demand_id' => $demand->id,
                'training_capacity_id' => $capacity->id,
                'quantity' => $this->quantity($proposal),
                'plan' => Str::limit($plan, 2000, ''),
                'status' => $publish ? 'SUBMITTED' : 'WITHDRAWN',
                'source' => 'APPS_SCRIPT',
                'is_sample' => $this->isSample($proposal),
            ],
        );
    }

    private function resolveEmployer(string $name): Employer
    {
        $name = trim($name);
        if (mb_strlen($name) < 3) {
            throw new \RuntimeException('thiếu tên doanh nghiệp');
        }

        $normalized = $this->normalize($name);
        $employer = Employer::query()->get()->first(fn (Employer $item): bool => $this->normalize($item->name) === $normalized);

        return $employer ?? Employer::query()->create([
            'name' => Str::limit($name, 200, ''),
            'status' => 'ACTIVE',
            'is_verified' => false,
            'source' => 'APPS_SCRIPT',
        ]);
    }

    private function resolveSchool(string $name): School
    {
        $normalized = $this->normalize($name);
        $school = School::query()->get(['id', 'name'])->first(fn (School $item): bool => $this->normalize($item->name) === $normalized);

        if (! $school) {
            throw new \RuntimeException("không tìm thấy trường/cơ sở '{$name}' trong hệ thống");
        }

        return $school;
    }

    private function resolveMajor(string $name): TrainingMajor
    {
        $name = trim($name);
        if (mb_strlen($name) < 2) {
            throw new \RuntimeException('thiếu ngành/nghề');
        }

        $slug = Str::slug($name);

        return TrainingMajor::query()->firstOrCreate(
            ['slug' => $slug],
            ['name' => Str::limit($name, 255, ''), 'is_active' => true],
        );
    }

    private function resolveLevel(string $name): TrainingLevel
    {
        $normalized = $this->normalize($name);
        $level = TrainingLevel::query()->get()->first(fn (TrainingLevel $item): bool => $this->normalize($item->name) === $normalized);
        if (! $level) {
            throw new \RuntimeException("không nhận diện được trình độ '{$name}'");
        }

        return $level;
    }

    private function resolvePeriod(string $name): ReportingPeriod
    {
        $normalized = $this->normalize($name);
        $period = ReportingPeriod::query()->get()->first(fn (ReportingPeriod $item): bool => $this->normalize($item->name) === $normalized || $this->normalize($item->code) === $normalized);
        if (! $period) {
            throw new \RuntimeException("không tìm thấy kỳ báo cáo '{$name}'");
        }

        return $period;
    }

    /** @return array<int, int> */
    private function resolveSkillIds(mixed $skills): array
    {
        if (! is_array($skills)) {
            return [];
        }

        return collect($skills)
            ->map(fn ($name): string => trim((string) $name))
            ->filter()
            ->unique(fn (string $name): string => Skill::normalize($name))
            ->take(20)
            ->map(function (string $name): int {
                $skill = Skill::query()->firstOrCreate(
                    ['normalized_name' => Skill::normalize($name)],
                    ['name' => Str::limit($name, 120, ''), 'is_active' => true],
                );

                return (int) $skill->id;
            })
            ->values()
            ->all();
    }

    /** @param array<string, mixed> $record */
    private function externalId(array $record): string
    {
        $id = trim((string) ($record['id'] ?? ''));
        if ($id === '') {
            throw new \RuntimeException('thiếu mã bản ghi');
        }

        return $this->prefixedExternalId($id);
    }

    private function prefixedExternalId(string $id): string
    {
        $id = trim($id);
        if ($id === '') {
            throw new \RuntimeException('thiếu mã liên kết');
        }

        return Str::limit('apps-script:'.$id, 100, '');
    }

    /** @param array<string, mixed> $record */
    private function quantity(array $record): int
    {
        $quantity = filter_var($record['quantity'] ?? null, FILTER_VALIDATE_INT);
        if ($quantity === false || $quantity < 1 || $quantity > 100000) {
            throw new \RuntimeException('số lượng phải từ 1 đến 100.000');
        }

        return $quantity;
    }

    private function purpose(string $purpose): string
    {
        return match ($this->normalize($purpose)) {
            'dao tao moi', 'new training' => 'NEW_TRAINING',
            'dao tao lai', 'retraining' => 'RETRAINING',
            default => 'RECRUITMENT',
        };
    }

    /** @param array<string, mixed> $record */
    private function isSample(array $record): bool
    {
        return filter_var($record['sample'] ?? false, FILTER_VALIDATE_BOOL);
    }

    private function nullableText(mixed $value, int $limit): ?string
    {
        $value = trim((string) $value);

        return $value === '' ? null : Str::limit($value, $limit, '');
    }

    private function nullablePositiveInteger(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        $number = filter_var($value, FILTER_VALIDATE_INT);

        return $number !== false && $number >= 0 ? $number : null;
    }

    private function nullableDate(mixed $value): ?string
    {
        $value = trim((string) $value);

        return preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) ? $value : null;
    }

    private function normalize(string $value): string
    {
        return Str::of($value)->ascii()->lower()->squish()->toString();
    }

    /** @param array<string, mixed> $record */
    private function recordError(string $type, array $record, Throwable $exception): string
    {
        $id = trim((string) ($record['id'] ?? '?'));

        return "{$type} {$id}: {$exception->getMessage()}";
    }

    /** @param array<string, mixed> $payload @return array<string, mixed> */
    private function unwrapPayload(array $payload): array
    {
        if (isset($payload['body']) && is_array($payload['body'])) {
            return $payload['body'];
        }

        if (isset($payload['data']) && is_array($payload['data'])) {
            return $payload['data'];
        }

        return $payload;
    }
}
