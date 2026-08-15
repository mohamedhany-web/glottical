<?php

namespace App\Console\Commands;

use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumPresentationDerivative;
use App\Services\CurriculumPresentationConversionService;
use Illuminate\Console\Command;

class ConvertCurriculumPresentationsCommand extends Command
{
    protected $signature = 'curriculum:convert-presentations
                            {--material= : Convert a single curriculum library material id}
                            {--retry-failed : Re-queue materials with failed derivatives}
                            {--retry-unavailable : Re-queue materials with unavailable derivatives}
                            {--limit=50 : Max materials to enqueue}
                            {--sync : Run conversion synchronously instead of queueing}';

    protected $description = 'Backfill or retry PPT/PPTX curriculum presentation slide derivatives (never mutates originals)';

    public function handle(CurriculumPresentationConversionService $service): int
    {
        if (! $service->derivativesTableReady()) {
            $this->error('Table curriculum_presentation_derivatives is missing. Run migrations first.');

            return self::FAILURE;
        }

        if (! $service->isConversionEnabled()) {
            $this->warn('Curriculum presentation conversion is disabled via config.');

            return self::SUCCESS;
        }

        $limit = max(1, (int) $this->option('limit'));
        $sync = (bool) $this->option('sync');
        $materialId = $this->option('material');

        if ($materialId !== null && $materialId !== '') {
            $material = CurriculumLibraryMaterial::query()->find((int) $materialId);
            if (! $material) {
                $this->error('Material not found: '.$materialId);

                return self::FAILURE;
            }
            if (! $service->materialIsConvertible($material)) {
                $this->warn('Material is not a convertible PPT/PPTX presentation.');

                return self::SUCCESS;
            }

            $service->queueMaterialIfEligible($material, $sync);
            $this->info(($sync ? 'Converted' : 'Queued').' material #'.$material->id);

            return self::SUCCESS;
        }

        $query = CurriculumLibraryMaterial::query()
            ->where('file_kind', 'pptx')
            ->orderBy('id');

        if ($this->option('retry-failed') || $this->option('retry-unavailable')) {
            $statuses = [];
            if ($this->option('retry-failed')) {
                $statuses[] = CurriculumPresentationDerivative::STATUS_FAILED;
            }
            if ($this->option('retry-unavailable')) {
                $statuses[] = CurriculumPresentationDerivative::STATUS_UNAVAILABLE;
            }

            $ids = CurriculumPresentationDerivative::query()
                ->where('source_type', CurriculumPresentationDerivative::SOURCE_MATERIAL)
                ->whereIn('status', $statuses)
                ->pluck('source_id');

            $query->whereIn('id', $ids);
        } else {
            // Backfill: materials with no derivative row, or non-ready statuses excluding processing.
            $readyOrProcessing = CurriculumPresentationDerivative::query()
                ->where('source_type', CurriculumPresentationDerivative::SOURCE_MATERIAL)
                ->whereIn('status', [
                    CurriculumPresentationDerivative::STATUS_READY,
                    CurriculumPresentationDerivative::STATUS_PROCESSING,
                    CurriculumPresentationDerivative::STATUS_PENDING,
                ])
                ->pluck('source_id');

            $query->whereNotIn('id', $readyOrProcessing);
        }

        $materials = $query->limit($limit)->get();
        if ($materials->isEmpty()) {
            $this->info('No materials matched.');

            return self::SUCCESS;
        }

        $count = 0;
        foreach ($materials as $material) {
            if (! $service->materialIsConvertible($material)) {
                continue;
            }
            $service->queueMaterialIfEligible($material, $sync);
            $count++;
            $this->line(($sync ? 'Converted' : 'Queued').' material #'.$material->id);
        }

        $this->info("Done. Processed {$count} material(s).");

        return self::SUCCESS;
    }
}
