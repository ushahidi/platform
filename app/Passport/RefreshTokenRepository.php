<?php

namespace Ushahidi\App\Passport;

use Laravel\Passport\Bridge\RefreshTokenRepository as LaravelRefreshTokenRepository;

// This is an ugly hack to prevent refresh tokens being single use
// or revoked just because the access token was revoked
class RefreshTokenRepository extends LaravelRefreshTokenRepository
{
    /**
     * {@inheritdoc}
     */
    public function revokeRefreshToken($tokenId)
    {
        // Noop
        // $this->database->table('oauth_refresh_tokens')
        //             ->where('id', $tokenId)->update(['revoked' => true]);
    }

    /**
     * {@inheritdoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        $refreshToken = $this->database->table('oauth_refresh_tokens')
                    ->where('id', $tokenId)->first();

        if ($refreshToken === null || $refreshToken->revoked) {
            return true;
        }

        return false;
    }
}
