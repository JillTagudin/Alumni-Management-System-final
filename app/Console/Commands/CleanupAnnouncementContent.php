<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanupAnnouncementContent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cleanup:announcement-content {--max-length=65535 : Maximum length for content}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup announcement content that is too long for TEXT column type';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $maxLength = $this->option('max-length');
        
        $this->info("Checking for announcement content longer than {$maxLength} characters...");
        
        // Find announcements with content longer than the specified limit
        $longAnnouncements = DB::table('announcements')
            ->whereRaw('LENGTH(content) > ?', [$maxLength])
            ->get(['id', 'title']);
        
        if ($longAnnouncements->isEmpty()) {
            $this->info('No announcements found with content exceeding the limit.');
            return 0;
        }
        
        $this->warn("Found {$longAnnouncements->count()} announcements with content exceeding {$maxLength} characters:");
        
        foreach ($longAnnouncements as $announcement) {
            $this->line("- ID: {$announcement->id}, Title: {$announcement->title}");
        }
        
        if ($this->confirm('Do you want to truncate the content of these announcements?')) {
            $updated = 0;
            
            foreach ($longAnnouncements as $announcement) {
                // Get the current content
                $currentContent = DB::table('announcements')
                    ->where('id', $announcement->id)
                    ->value('content');
                
                // Truncate content and add indication that it was truncated
                $truncatedContent = substr($currentContent, 0, $maxLength - 100) . '\n\n[Content truncated due to length limit]';
                
                // Update the announcement
                DB::table('announcements')
                    ->where('id', $announcement->id)
                    ->update(['content' => $truncatedContent]);
                
                $updated++;
                $this->info("Truncated content for announcement ID: {$announcement->id}");
            }
            
            $this->info("Successfully truncated content for {$updated} announcements.");
        } else {
            $this->info('Operation cancelled.');
        }
        
        return 0;
    }
}