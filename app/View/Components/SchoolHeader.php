<?php

namespace App\View\Components;

use Closure;
use App\Models\SchoolSetting;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class SchoolHeader extends Component
{
    public $settings;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->settings = SchoolSetting::first();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.school-header');
    }
}
