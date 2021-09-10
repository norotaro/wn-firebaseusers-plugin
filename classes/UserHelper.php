<?php

namespace Norotaro\FirebaseUsers\Classes;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Kreait\Firebase\Auth\UserRecord;

class UserHelper
{
    use \Winter\Storm\Support\Traits\Singleton;

    public function userToArray(UserRecord $user): array
    {
        $userEmail = $this->getUserEmail($user);

        return [
            'name' => $user->displayName,
            'email' => $userEmail,
            'password' => Str::random(8),
            'is_activated' => $user->emailVerified,
            'activated_at' => $user->emailVerified ? Carbon::instance($user->metadata->createdAt) : null,
            'last_login' => $user->metadata->lastLoginAt ? Carbon::instance($user->metadata->lastLoginAt) : null,
            'created_at' => $user->metadata->createdAt ? Carbon::instance($user->metadata->createdAt) : null,
            'updated_at' => $user->metadata->lastRefreshAt ? Carbon::instance($user->metadata->lastRefreshAt) : null,
            'deleted_at' => $user->disabled ? Carbon::now() : null,
            'last_seen' => $user->metadata->lastLoginAt ? Carbon::instance($user->metadata->lastLoginAt) : null,
            'username' => $userEmail ?: $user->displayName ?: $user->uid,
            'fb_uid' => $user->uid,
            'fb_sync' => true,
        ];
    }

    protected function getUserEmail(UserRecord $user): string|null
    {
        if (empty($user->email)) {
            foreach ($user->providerData as $provider) {
                if (!empty($provider->email)) {
                    return $provider->email;
                }
            }
        }

        return $user->email;
    }
}
