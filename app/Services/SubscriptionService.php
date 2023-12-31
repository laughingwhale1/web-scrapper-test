<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionService
{

    public function __construct(
        private UserService     $userService,
        private PropertyService $propertyService
    )
    {

    }

    public function handleNewSubscription($subscription)
    {
        $userId = $this->userService->isUserExist($subscription['email']);
        $propertyId = $this->propertyService->isPropertyExist($subscription['url']);

        return $this->isSubscriptionExists($userId, $propertyId);
    }

    public function isSubscriptionExists(int $userId, int $propertyId)
    {
        $exists = DB::table('property_user')
            ->where('user_id', $userId)
            ->where('property_id', $propertyId)
            ->exists();

        if ($exists) {
            Log::info('exists');
            return response(['message' => 'subscription already exists'], 400);
        }

        /** @var User $user */
        $user = User::query()->find($userId);
        $user->properties()->attach($propertyId);
        Log::info('not exists, created');
        return response(['message' => 'subscription created'], 201);
    }
}
