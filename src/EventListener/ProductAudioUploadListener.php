<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\Product\ProductAudio;
use Doctrine\Bundle\DoctrineBundle\Attribute\AsEntityListener;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Component\HttpFoundation\File\UploadedFile;

#[AsEntityListener(event: Events::prePersist, method: 'uploadAudio', entity: ProductAudio::class)]
#[AsEntityListener(event: Events::preUpdate, method: 'uploadAudioOnUpdate', entity: ProductAudio::class)]
#[AsEntityListener(event: Events::postRemove, method: 'removePhysicalFile', entity: ProductAudio::class)]
final class ProductAudioUploadListener
{
    private string $uploadDir;

    public function __construct(
        #[Autowire('%kernel.project_dir%')] string $projectDir
    ) {
        $this->uploadDir = $projectDir . '/public/media/audio';
    }

    public function uploadAudio(ProductAudio $audio, PrePersistEventArgs $event): void
    {
        $this->handleFileUpload($audio);
    }

    public function uploadAudioOnUpdate(ProductAudio $audio, PreUpdateEventArgs $event): void
    {
        $this->handleFileUpload($audio);
    }

    public function removePhysicalFile(ProductAudio $audio, PostRemoveEventArgs $event): void
    {
        $path = $audio->getPath();
        if (null !== $path && '' !== $path) {
            $filePath = $this->uploadDir . '/' . $path;
            if (file_exists($filePath)) {
                @unlink($filePath);
            }
        }
    }

    private function handleFileUpload(ProductAudio $audio): void
    {
        $file = $audio->getFile();
        if (!$file instanceof UploadedFile) {
            return;
        }

        if (!is_dir($this->uploadDir)) {
            @mkdir($this->uploadDir, 0777, true);
        }

        $filename = sprintf('%s-%s.%s', uniqid('audio_', true), bin2hex(random_bytes(4)), $file->guessExtension() ?: 'mp3');

        $audio->setOriginalName($file->getClientOriginalName());
        $audio->setMimeType($file->getClientMimeType() ?: $file->getMimeType());
        $audio->setSize($file->getSize());
        $audio->setPath($filename);

        $file->move($this->uploadDir, $filename);
        $audio->setFile(null);
    }
}
