<?php

namespace ticketeradigital\Hashable;

trait Hashable
{
    private function encodeToUrlSafe($binaryHash): string
    {
        $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $base = strlen($allowedChars);
        $encoded = '';

        $binaryLength = strlen($binaryHash);
        for ($i = 0; $i < $binaryLength; $i++) {
            $byteValue = ord($binaryHash[$i]);
            $encoded .= $allowedChars[$byteValue % $base];
        }

        return $encoded;
    }

    public function getPlainHashAttribute(): string
    {
        $pool = [
            $this->id,
            $this->uuid ?? '',
            $this->created_at->toISOString(),
            config('app.hashableSalt'),
        ];

        return hash('sha256', implode('', $pool), true);
    }

    public function getHashAttribute(): string
    {
        return $this->encodeToUrlSafe(base64_encode($this->plainHash));
    }

    public function verifyHash(?string $hash): bool
    {
        if (! $hash) {
            return false;
        }

        return $this->hash === $hash;
    }
}

