<?php

namespace App\Observers;


class SectionObserver
{
    public function deleting(Section $section)
    {
        foreach ($section->subSections as $subSection) {
            $subSection->delete();
        }
    }
}
