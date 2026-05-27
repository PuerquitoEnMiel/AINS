<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Create an FTS5 virtual table for full-text search on tools.
 * FTS5 is a SQLite extension that provides fast, efficient full-text search.
 * The content-sync'd approach keeps the FTS index automatically updated
 * via triggers whenever the tools table changes.
 */
return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            // Content-sync'd FTS5 table — points to the real 'tools' table
            DB::statement("
                CREATE VIRTUAL TABLE IF NOT EXISTS tools_fts USING fts5(
                    name,
                    description,
                    content='tools',
                    content_rowid='id'
                )
            ");

            // Triggers to keep FTS index in sync with tools table
            DB::statement("
                CREATE TRIGGER IF NOT EXISTS tools_ai AFTER INSERT ON tools BEGIN
                    INSERT INTO tools_fts(rowid, name, description)
                    VALUES (new.id, new.name, new.description);
                END
            ");

            DB::statement("
                CREATE TRIGGER IF NOT EXISTS tools_ad AFTER DELETE ON tools BEGIN
                    INSERT INTO tools_fts(tools_fts, rowid, name, description)
                    VALUES ('delete', old.id, old.name, old.description);
                END
            ");

            DB::statement("
                CREATE TRIGGER IF NOT EXISTS tools_au AFTER UPDATE ON tools BEGIN
                    INSERT INTO tools_fts(tools_fts, rowid, name, description)
                    VALUES ('delete', old.id, old.name, old.description);
                    INSERT INTO tools_fts(rowid, name, description)
                    VALUES (new.id, new.name, new.description);
                END
            ");

            // Populate FTS index with existing data
            DB::statement("
                INSERT INTO tools_fts(rowid, name, description)
                SELECT id, name, description FROM tools
            ");
        }

        // For PostgreSQL (future-proof): add GIN index on tsvector
        if ($driver === 'pgsql') {
            DB::statement("
                ALTER TABLE tools
                ADD COLUMN IF NOT EXISTS search_vector tsvector
                GENERATED ALWAYS AS (
                    to_tsvector('english', coalesce(name, '') || ' ' || coalesce(description, ''))
                ) STORED
            ");
            DB::statement("
                CREATE INDEX IF NOT EXISTS tools_search_idx ON tools USING GIN (search_vector)
            ");
        }
    }

    public function down(): void
    {
        $driver = DB::getDriverName();

        if ($driver === 'sqlite') {
            DB::statement("DROP TRIGGER IF EXISTS tools_au");
            DB::statement("DROP TRIGGER IF EXISTS tools_ad");
            DB::statement("DROP TRIGGER IF EXISTS tools_ai");
            DB::statement("DROP TABLE IF EXISTS tools_fts");
        }

        if ($driver === 'pgsql') {
            DB::statement("DROP INDEX IF EXISTS tools_search_idx");
            DB::statement("ALTER TABLE tools DROP COLUMN IF EXISTS search_vector");
        }
    }
};
