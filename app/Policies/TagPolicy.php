<?php


namespace App\Policies;

use App\Models\User;
use App\Models\Tag;

class TagPolicy
{
    /**
     * Determine whether the user can view the tag.
     */
    public function view(User $user, Tag $tag): bool
    {
        return $tag->company_id === $user->company_id;
    }

    /**
     * Determine whether the user can create tags.
     */
    public function create(User $user): bool
    {
        return $user->hasPermission('manage_tags');
    }

    /**
     * Determine whether the user can update the tag.
     */
    public function update(User $user, Tag $tag): bool
    {
        return $tag->company_id === $user->company_id
            && ($tag->created_by === $user->id || $user->hasPermission('manage_tags'));
    }

    /**
     * Determine whether the user can delete the tag.
     */
    public function delete(User $user, Tag $tag): bool
    {
        return $tag->company_id === $user->company_id
            && ($tag->created_by === $user->id || $user->hasPermission('manage_tags'));
    }
}
