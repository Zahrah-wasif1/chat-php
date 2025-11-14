<?php
namespace App\Controllers;

use App\Helpers\Database;
use App\Models\AuditLog;

class ModerationController
{
    // Get all audit logs
    public function getAuditLogs()
    {
        $logs = AuditLog::getAll();
        echo json_encode($logs);
    }
}
