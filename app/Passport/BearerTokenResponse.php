<?php
/**
 * OAuth 2.0 Bearer Token Response.
 */

namespace Ushahidi\App\Passport;

use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\ResponseTypes\BearerTokenResponse as LeagueBearerTokenResponse;

class BearerTokenResponse extends LeagueBearerTokenResponse
{
    /**
     * Add custom fields to your Bearer Token response here, then override
     * AuthorizationServer::getResponseType() to pull in your version of
     * this class rather than the default.
     *
     * @param AccessTokenEntityInterface $accessToken
     *
     * @return array
     */
    protected function getExtraParams(AccessTokenEntityInterface $accessToken)
    {
        $expireDateTime = $this->accessToken->getExpiryDateTime()->getTimestamp();
        return [
            // Add expires time for backwards compat
            'expires'   => $expireDateTime,
        ];
    }
}
