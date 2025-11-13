<?php
namespace App\Controllers;
use App\Database;
use App\Models\AuditLog;

class ModerationController
{
    public function getAuditLogs()
    {
        echo json_encode(AuditLog::getAll());
    }
}
