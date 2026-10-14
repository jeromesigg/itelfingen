<?php

namespace App\Enums;

/**
 * Archiv-Status von Inhalten (Tabelle archive_statuses, Spalte archive_status_id).
 */
enum ArchiveState: int
{
    case Aktiv = 1;
    case Archiviert = 2;
}
