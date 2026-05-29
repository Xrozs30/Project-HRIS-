<?php
\App\Models\User::where('role', 'hr')->update(['position' => 'HR']);
echo 'HR positions updated.';
