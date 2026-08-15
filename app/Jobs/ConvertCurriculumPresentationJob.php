<?php

namespace App\Jobs;

use App\Models\CurriculumLibraryMaterial;
use App\Models\CurriculumPresentationDerivative;
use App\Services\CurriculumPresentationConversionService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Throwable;

class ConvertCurriculumPresentationJob implements ShouldBeUnique, ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries;

    public int $timeout;

    /** @var list<int> */
    public array $backoff;

    public int $uniqueFor = 3600;

    public function __construct(
        public string $sourceType,
        public int $sourceId
    ) {
        $this->tries = max(1, (int) config('curriculum_presentation.job_tries', 3));
        $this->timeout = max(60, (int) config('curriculum_presentation.timeout_seconds', 600) + 60);
        $backoff = config('curriculum_presentation.job_backoff_seconds', [30, 90, 180]);
        $this->backoff = is_array($backoff) && $backoff !== [] ? array_values($backoff) : [30, 90, 180];
        $this->onQueue((string) config('curriculum_presentation.queue', 'default'));
    }

    public function uniqueId(): string
    {
        return 'curriculum-presentation-convert:'.$this->sourceType.':'.$this->sourceId;
    }

    public function handle(CurriculumPresentationConversionService $service): void
    {
        if (! $service->derivativesTableReady() || ! $service->isConversionEnabled()) {
            return;
        }

        if ($this->sourceType !== CurriculumPresentationDerivative::SOURCE_MATERIAL) {
            return;
        }

        $material = CurriculumLibraryMaterial::query()->find($this->sourceId);
        if (! $material) {
            return;
        }

        if (! $service->materialIsConvertible($material)) {
            return;
        }

        $derivative = CurriculumPresentationDerivative::query()
            ->where('source_type', CurriculumPresentationDerivative::SOURCE_MATERIAL)
            ->where('source_id', $material->id)
            ->first();

        // Idempotency: skip if already ready for current source object identity.
        if ($derivative
            && $derivative->status === CurriculumPresentationDerivative::STATUS_READY
            && $derivative->manifest_path
        ) {
            return;
        }

        $service->convertMaterial($material);
    }

    public function failed(?Throwable $exception): void
    {
        try {
            $derivative = CurriculumPresentationDerivative::query()
                ->where('source_type', $this->sourceType)
                ->where('source_id', $this->sourceId)
                ->first();

            if ($derivative && $derivative->status !== CurriculumPresentationDerivative::STATUS_READY) {
                $derivative->fill([
                    'status' => CurriculumPresentationDerivative::STATUS_FAILED,
                    'error_message' => mb_substr($exception?->getMessage() ?? 'Conversion job failed', 0, 2000),
                ]);
                $derivative->save();
            }
        } catch (Throwable $e) {
            Log::error('ConvertCurriculumPresentationJob failed handler error', [
                'source_type' => $this->sourceType,
                'source_id' => $this->sourceId,
                'message' => $e->getMessage(),
            ]);
        }

        Log::error('ConvertCurriculumPresentationJob failed', [
            'source_type' => $this->sourceType,
            'source_id' => $this->sourceId,
            'message' => $exception?->getMessage(),
        ]);
    }
}
