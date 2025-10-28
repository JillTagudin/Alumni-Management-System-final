<?php

namespace App\Console\Commands;

use App\Models\Announcement;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class FixAnnouncementAttachments extends Command
{
    protected $signature = 'announcements:fix-attachments';
    protected $description = 'Fix existing announcement attachments by adding missing mime_type data';

    public function handle()
    {
        $this->info('Starting to fix announcement attachments...');
        
        $announcements = Announcement::whereNotNull('attachments')->get();
        $fixed = 0;
        $total = $announcements->count();
        
        $this->info("Found {$total} announcements with attachments to process.");
        
        foreach ($announcements as $announcement) {
            $attachments = json_decode($announcement->attachments, true);
            
            if (!is_array($attachments)) {
                continue;
            }
            
            $updated = false;
            
            foreach ($attachments as &$attachment) {
                // Check if mime_type is missing
                if (!isset($attachment['mime_type']) || empty($attachment['mime_type'])) {
                    $filePath = storage_path('app/public/' . $attachment['path']);
                    
                    if (file_exists($filePath)) {
                        // Try to get mime type from file
                        $mimeType = mime_content_type($filePath);
                        
                        if ($mimeType) {
                            $attachment['mime_type'] = $mimeType;
                            $updated = true;
                            $this->line("Fixed mime_type for: {$attachment['name']} -> {$mimeType}");
                        } else {
                            // Fallback: guess from extension
                            $extension = strtolower(pathinfo($attachment['name'], PATHINFO_EXTENSION));
                            $mimeType = $this->getMimeTypeFromExtension($extension);
                            
                            if ($mimeType) {
                                $attachment['mime_type'] = $mimeType;
                                $updated = true;
                                $this->line("Guessed mime_type for: {$attachment['name']} -> {$mimeType}");
                            }
                        }
                    } else {
                        $this->warn("File not found: {$filePath}");
                    }
                }
            }
            
            if ($updated) {
                $announcement->update(['attachments' => json_encode($attachments)]);
                $fixed++;
            }
        }
        
        $this->info("Fixed {$fixed} out of {$total} announcements.");
        return 0;
    }
    
    private function getMimeTypeFromExtension($extension)
    {
        $mimeTypes = [
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'avi' => 'video/avi',
            'mov' => 'video/quicktime',
            'pdf' => 'application/pdf',
            'doc' => 'application/msword',
            'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'xls' => 'application/vnd.ms-excel',
            'xlsx' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'ppt' => 'application/vnd.ms-powerpoint',
            'pptx' => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
            'txt' => 'text/plain',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];
        
        return $mimeTypes[$extension] ?? 'application/octet-stream';
    }
}