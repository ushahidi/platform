<?php

namespace App\Passport;

use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid as UUID;
use Laravel\Passport\Passport;
use Laravel\Passport\PersonalAccessClient;
use Laravel\Passport\Client as LaravelPassportClient;
use Laravel\Passport\ClientRepository as LaravelPassportClientRepository;

class ClientRepository extends LaravelPassportClientRepository
{
    /**
     * Get a client by the given ID.
     *
     * @param  int  $id
     * @return Client|null
     */
    public function find($id)
    {
        return Client::find($id);
    }

    /**
     * Get an active client by the given ID.
     *
     * @param  int  $id
     * @return Client|null
     */
    public function findActive($id)
    {
        $client = $this->find($id);

        return $client && ! $client->revoked ? $client : null;
    }

    /**
     * Get a client instance for the given ID and user ID.
     *
     * @param  int  $clientId
     * @param  mixed  $userId
     * @return Client|null
     */
    public function findForUser($clientId, $userId)
    {
        return Client::where('id', $clientId)
                     ->where('user_id', $userId)
                     ->first();
    }

    /**
     * Get the client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function forUser($userId)
    {
        return Client::where('user_id', $userId)
                        ->orderBy('name', 'asc')->get();
    }

    /**
     * Get the active client instances for the given user ID.
     *
     * @param  mixed  $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function activeForUser($userId)
    {
        return $this->forUser($userId)->reject(function ($client) {
            return $client->revoked;
        })->values();
    }

    /**
     * Get the personal access token client for the application.
     *
     * @return Client
     */
    public function personalAccessClient()
    {
        if (Passport::$personalAccessClient) {
            return Client::find(Passport::$personalAccessClient);
        } else {
            return PersonalAccessClient::orderBy('id', 'desc')->first()->client;
        }
    }

    /**
     * Store a new client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @param  string|null  $provider
     * @param  bool  $personalAccess
     * @param  bool  $password
     * @param  bool  $confidential
     *
     * @return Client
     */
    public function create(
        $userId,
        $name,
        $redirect,
        $provider = null,
        $personalAccess = false,
        $password = false,
        $confidential = true
    ) {
        $client = (new Client)->forceFill([
            'user_id' => $userId,
            'name' => $name,
            'provider' => $provider,
            'secret' => ($confidential || $personalAccess) ? Str::random(40) : null,
            'redirect' => $redirect,
            'personal_access_client' => $personalAccess,
            'password_client' => $password,
            'revoked' => false,
            // Set id to UUID
            'id' => UUID::uuid4()->toString(),
        ]);

        $client->save();

        return $client;
    }

    /**
     * Store a new personal access token client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @return Client
     */
    public function createPersonalAccessClient($userId, $name, $redirect)
    {
        return $this->create($userId, $name, $redirect, true);
    }

    /**
     * Store a new password grant client.
     *
     * @param  int  $userId
     * @param  string  $name
     * @param  string  $redirect
     * @param  string|null  $provider
     *
     * @return Client
     */
    public function createPasswordGrantClient($userId, $name, $redirect, $provider = null)
    {
        return $this->create($userId, $name, $redirect, $provider, false, true);
    }

    /**
     * Update the given client.
     *
     * @param  Client  $client
     * @param  string  $name
     * @param  string  $redirect
     * @return Client
     */
    public function update(LaravelPassportClient $client, $name, $redirect)
    {
        $client->forceFill([
            'name' => $name, 'redirect' => $redirect,
        ])->save();

        return $client;
    }

    /**
     * Regenerate the client secret.
     *
     * @param  Client  $client
     * @return Client
     */
    public function regenerateSecret(LaravelPassportClient $client)
    {
        $client->forceFill([
            'secret' => Str::random(40),
        ])->save();

        return $client;
    }

    /**
     * Determine if the given client is revoked.
     *
     * @param  int  $id
     * @return bool
     */
    public function revoked($id)
    {
        return Client::where('id', $id)
                ->where('revoked', true)->exists();
    }

    /**
     * Delete the given client.
     *
     * @param  Client  $client
     * @return void
     */
    public function delete(LaravelPassportClient $client)
    {
        $client->tokens()->update(['revoked' => true]);

        $client->forceFill(['revoked' => true])->save();
    }
}
