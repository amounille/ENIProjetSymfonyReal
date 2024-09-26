<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;

class ImagesUploader
{
    public function __construct(
        private string $imagesDirectory,
        private SluggerInterface $slugger,
    ) {
    }

    public function upload(UploadedFile $file): string
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        // Filename - uuid
        $fileName = $safeFilename . '-' . uniqid() . '.' . $file->guessExtension();
        // Filename - datetime - uuid
        $fileName = $safeFilename . '-' . date('Ymd-His') . '-' . uniqid() . '.' . $file->guessExtension();
        // Date - uuid
        $fileName = date('Ymd-His') . '-' . uniqid() . '.' . $file->guessExtension();

        try {
            $file->move($this->getImagesDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }

        return $fileName;
    }

    public function getImagesDirectory(): string
    {
        return $this->imagesDirectory;
    }
}
