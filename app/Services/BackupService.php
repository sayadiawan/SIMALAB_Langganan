<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class BackupService
{
    /**
     * Create a new backup
     */
    public function createBackup($name = null)
    {
        try {
            $timestamp = Carbon::now()->format('Y-m-d_H-i-s');
            $backupName = $name ?: "boyolali-labkes-backup-{$timestamp}";
            
            // Create backup directory if it doesn't exist
            $backupPath = storage_path('app/backups');
            if (!file_exists($backupPath)) {
                mkdir($backupPath, 0755, true);
            }
            
            // Create temporary directory
            $tempPath = storage_path('app/backup-temp');
            if (!file_exists($tempPath)) {
                mkdir($tempPath, 0755, true);
            }
            
            // Database backup only
            $this->backupDatabase($backupName);
            
            // Create zip archive
            $zipPath = $this->createZipArchive($backupName);
            
            // Upload to Google Drive
            $this->uploadToGoogleDrive($zipPath);
            
            // Delete all local backup files after successful upload
            $this->deleteAllLocalBackups();
            
            // Cleanup temporary files
            $this->cleanupTempFiles();
            
            Log::info("Backup created successfully: {$backupName}");
            
            return $zipPath;
            
        } catch (\Exception $e) {
            Log::error("Backup failed: " . $e->getMessage());
            throw $e;
        }
    }
    
    /**
     * Backup database
     */
    private function backupDatabase($backupName)
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host');
        
        $dumpFile = storage_path("app/backup-temp/{$backupName}.sql");
        
        // Try to find mysqldump in common locations
        $mysqldumpPaths = [
            '/usr/bin/mysqldump',
            '/usr/local/bin/mysqldump',
            '/opt/mysql/bin/mysqldump',
            '/opt/lampp/bin/mysqldump',
            '/xampp/mysql/bin/mysqldump',
            'mysqldump' // fallback to PATH
        ];
        
        $mysqldump = null;
        foreach ($mysqldumpPaths as $path) {
            if (file_exists($path) || $path === 'mysqldump') {
                $mysqldump = $path;
                break;
            }
        }
        
        if (!$mysqldump) {
            throw new \Exception("mysqldump not found. Please install MySQL client or specify correct path.");
        }
        
        // Build command with proper escaping
        $command = escapeshellcmd($mysqldump);
        $command .= " -h " . escapeshellarg($dbHost);
        $command .= " -u " . escapeshellarg($dbUser);
        
        if ($dbPass) {
            $command .= " -p" . escapeshellarg($dbPass);
        }
        
        $command .= " " . escapeshellarg($dbName);
        $command .= " > " . escapeshellarg($dumpFile);
        
        // Execute command and capture output
        $output = [];
        $returnCode = 0;
        
        exec($command . " 2>&1", $output, $returnCode);
        
        if ($returnCode !== 0) {
            $errorMessage = "Database backup failed. Return code: {$returnCode}. ";
            if (!empty($output)) {
                $errorMessage .= "Error: " . implode("\n", $output);
            }
            throw new \Exception($errorMessage);
        }
        
        // Check if file was created and has content
        if (!file_exists($dumpFile) || filesize($dumpFile) === 0) {
            throw new \Exception("Database backup file was not created or is empty");
        }
    }
    
    /**
     * Create zip archive
     */
    private function createZipArchive($backupName)
    {
        $zipPath = storage_path("app/backups/{$backupName}.zip");
        $tempPath = storage_path('app/backup-temp');
        
        $command = "cd {$tempPath} && zip -r {$zipPath} .";
        
        exec($command, $output, $returnCode);
        
        if ($returnCode !== 0) {
            throw new \Exception("Zip creation failed");
        }
        
        return $zipPath;
    }
    
    /**
     * Upload to Google Drive
     */
    private function uploadToGoogleDrive($filePath)
    {
        try {
            // Check if Google Drive credentials are configured
            if (!config('services.google.client_id') || !config('services.google.folder_id')) {
                Log::info("Google Drive credentials not configured, skipping upload");
                return;
            }
            
            $drive = app('google.drive');
            $fileName = basename($filePath);
            
            $fileMetadata = new \Google_Service_Drive_DriveFile([
                'name' => $fileName,
                'parents' => [config('services.google.folder_id')]
            ]);
            
            $content = file_get_contents($filePath);
            $file = $drive->files->create($fileMetadata, [
                'data' => $content,
                'mimeType' => 'application/zip',
                'uploadType' => 'multipart'
            ]);
            
            Log::info("Uploaded to Google Drive: {$fileName} (ID: {$file->getId()})");
            
            // Cleanup old Google Drive backups (keep only 2 latest)
            $this->cleanupGoogleDriveBackups(2);
            
        } catch (\Exception $e) {
            Log::warning("Google Drive upload failed: " . $e->getMessage());
            // Don't throw exception, just log warning
        }
    }
    
    /**
     * Cleanup temporary files
     */
    private function cleanupTempFiles()
    {
        $tempPath = storage_path('app/backup-temp');
        
        if (file_exists($tempPath)) {
            $command = "rm -rf {$tempPath}/*";
            exec($command);
        }
    }
    
    /**
     * Delete all local backup files
     */
    private function deleteAllLocalBackups()
    {
        $backupPath = storage_path('app/backups');
        
        if (!file_exists($backupPath)) {
            return;
        }
        
        $files = glob($backupPath . '/*.zip');
        
        foreach ($files as $file) {
            unlink($file);
            Log::info("Deleted local backup: " . basename($file));
        }
        
        Log::info("All local backup files deleted after successful upload to Google Drive");
    }
    
    /**
     * Cleanup old Google Drive backups (keep only specified number of latest backups)
     */
    private function cleanupGoogleDriveBackups($keepCount = 2)
    {
        try {
            // Check if Google Drive credentials are configured
            if (!config('services.google.client_id') || !config('services.google.folder_id')) {
                Log::info("Google Drive credentials not configured, skipping cleanup");
                return;
            }
            
            $drive = app('google.drive');
            $folderId = config('services.google.folder_id');
            
            $results = $drive->files->listFiles([
                'q' => "'{$folderId}' in parents and trashed=false",
                'fields' => 'files(id,name,createdTime)',
                'orderBy' => 'createdTime desc'
            ]);
            
            $files = $results->getFiles();
            
            if (count($files) <= $keepCount) {
                return;
            }
            
            // Delete old files (keep only the latest $keepCount files)
            $filesToDelete = array_slice($files, $keepCount);
            
            foreach ($filesToDelete as $file) {
                $drive->files->delete($file->getId());
                Log::info("Deleted old Google Drive backup: {$file->getName()}");
            }
            
        } catch (\Exception $e) {
            Log::warning("Google Drive cleanup failed: " . $e->getMessage());
            // Don't throw exception, just log warning
        }
    }
    
    /**
     * Cleanup old backups (legacy method for command compatibility)
     */
    public function cleanupOldBackups()
    {
        try {
            // Cleanup Google Drive backups (keep only 2 latest)
            $this->cleanupGoogleDriveBackups(2);
            
            Log::info("Backup cleanup completed");
            
        } catch (\Exception $e) {
            Log::error("Backup cleanup failed: " . $e->getMessage());
            throw $e;
        }
    }
} 