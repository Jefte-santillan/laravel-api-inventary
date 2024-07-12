<?php

namespace App\Kernel\Models;

trait AuditableCustom
{
    use \OwenIt\Auditing\Auditable;

    public function transformAudit(array $data): array
    {
        // necesary override default include tags
        unset($data['tags']);
        return $data;
    }

    public function getAudits($id)
    {
        $record = $this->find($id);
        if (! $record) {
            return collect([]);
        }
        $records = $record->audits()->with([
            'user' => fn ($query) => $query->select('id', 'display')
        ])->get();
        return $records->map(function ($record) {
            $newRecord = $record->only([
                'id',
                'event',
                'old_values',
                'new_values',
                'ip_address',
                'created_at',
                'user'
            ]);
            $user = $newRecord['user'];
            $newRecord ['identity'] = $user;
            unset($newRecord['user']);
            return $newRecord;
        });
    }

}
