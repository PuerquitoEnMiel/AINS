<?php

namespace App\Observers;

use App\Models\Tool;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ToolObserver
{
    /**
     * Handle the Tool "saved" event.
     */
    public function saved(Tool $tool): void
    {
        $this->syncFts($tool);
    }

    /**
     * Handle the Tool "deleted" event.
     */
    public function deleted(Tool $tool): void
    {
        $this->deleteFts($tool);
    }

    /**
     * Sync the FTS index for a tool.
     */
    protected function syncFts(Tool $tool): void
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                // Remove existing
                DB::statement("DELETE FROM tools_fts WHERE rowid = ?", [$tool->id]);
                
                // Index tool
                DB::statement(
                    "INSERT INTO tools_fts (rowid, name, description) VALUES (?, ?, ?)",
                    [$tool->id, $tool->name, $tool->description]
                );
            }
        } catch (\Exception $e) {
            Log::error('FTS synchronization failed in ToolObserver', [
                'tool_id' => $tool->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Delete the FTS index for a tool.
     */
    protected function deleteFts(Tool $tool): void
    {
        try {
            $driver = DB::getDriverName();
            if ($driver === 'sqlite') {
                DB::statement("DELETE FROM tools_fts WHERE rowid = ?", [$tool->id]);
            }
        } catch (\Exception $e) {
            Log::error('FTS deletion failed in ToolObserver', [
                'tool_id' => $tool->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}
