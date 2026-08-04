<?php

namespace App\Observers;

use App\Models\StudentInstructorAssignment;
use App\Services\PrivateCoursesCoreService;

class StudentInstructorAssignmentObserver
{
    public function created(StudentInstructorAssignment $assignment): void
    {
        if ($assignment->status === StudentInstructorAssignment::STATUS_ACTIVE) {
            PrivateCoursesCoreService::notifyAssignment($assignment);
        }
    }

    public function updated(StudentInstructorAssignment $assignment): void
    {
        if ($assignment->wasChanged('status')
            && $assignment->status === StudentInstructorAssignment::STATUS_ACTIVE
            && ! $assignment->instructor_notified_at) {
            PrivateCoursesCoreService::notifyAssignment($assignment);
        }
    }
}
