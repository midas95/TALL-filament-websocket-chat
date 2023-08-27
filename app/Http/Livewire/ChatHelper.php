<?php
namespace App\Http\Livewire;


use Illuminate\Support\Str;

trait ChatHelper {
    public function getAvatarUrl($str): string
    {
        $name = Str::of($str)
            ->trim()
            ->explode(' ')
            ->map(fn (string $segment): string => filled($segment) ? mb_substr($segment, 0, 1) : '')
            ->join(' ');

        return 'https://ui-avatars.com/api/?name=' . urlencode($name) . '&color=FFFFFF&background=111827';
    }
    public function isOnline($str): bool
    {
        return \Carbon\Carbon::parse($str)->diffInMinutes() < 5;
    }

    /**
     * emphasize search word in string.
     */
    public function emphasize($string, $word)
    {
        $index1 = stripos($string, $word);
        $index2 = strlen($word);
        $res = substr($string, 0, $index1) . '<b>' . substr($string, $index1, $index2) . '</b>' . substr($string, $index1 + $index2);
        return str_replace(' ', '&nbsp;', $res);
    }
}
