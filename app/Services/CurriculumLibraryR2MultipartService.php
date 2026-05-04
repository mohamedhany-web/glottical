<?php

namespace App\Services;

use Aws\S3\S3Client;
use Illuminate\Support\Facades\Log;
use Throwable;

/**
 * رفع متعدد الأجزاء إلى Cloudflare R2 (واجهة S3).
 */
class CurriculumLibraryR2MultipartService
{
    public function __construct(
        protected string $diskName = 'r2'
    ) {}

    public function client(): S3Client
    {
        $c = config('filesystems.disks.'.$this->diskName);

        return new S3Client([
            'version' => 'latest',
            'region' => $c['region'] ?? 'auto',
            'credentials' => [
                'key' => $c['key'] ?? '',
                'secret' => $c['secret'] ?? '',
            ],
            'endpoint' => $c['endpoint'] ?? null,
            'use_path_style_endpoint' => (bool) ($c['use_path_style_endpoint'] ?? true),
        ]);
    }

    public function bucket(): string
    {
        $c = config('filesystems.disks.'.$this->diskName);

        return (string) ($c['bucket'] ?? '');
    }

    /**
     * @return array{UploadId: string, Key: string, Bucket: string}
     */
    public function createMultipartUpload(string $key, string $contentType): array
    {
        $client = $this->client();
        $bucket = $this->bucket();
        $result = $client->createMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'ContentType' => $contentType,
        ]);

        return [
            'UploadId' => (string) $result['UploadId'],
            'Key' => $key,
            'Bucket' => $bucket,
        ];
    }

    /**
     * @return array{url: string, headers: array<string, array<string>>}
     */
    public function presignedUploadPart(string $bucket, string $key, string $uploadId, int $partNumber, string $expires = '+24 hours'): array
    {
        $cmd = $this->client()->getCommand('UploadPart', [
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'PartNumber' => $partNumber,
        ]);
        $signed = $this->client()->createPresignedRequest($cmd, $expires);

        return [
            'url' => (string) $signed->getUri(),
            'headers' => $signed->getHeaders(),
        ];
    }

    /**
     * @param  array<int, array{PartNumber: int, ETag: string}>  $parts
     */
    public function completeMultipartUpload(string $bucket, string $key, string $uploadId, array $parts): void
    {
        $this->client()->completeMultipartUpload([
            'Bucket' => $bucket,
            'Key' => $key,
            'UploadId' => $uploadId,
            'MultipartUpload' => [
                'Parts' => $parts,
            ],
        ]);
    }

    public function abortMultipartUpload(string $bucket, string $key, string $uploadId): void
    {
        try {
            $this->client()->abortMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $key,
                'UploadId' => $uploadId,
            ]);
        } catch (Throwable $e) {
            Log::warning('R2 multipart abort', ['message' => $e->getMessage()]);
        }
    }
}
