<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Database\Factories\RoleFactory;
use Domain\Model\Role\Role as RoleDomainModel;
use Domain\Model\User\User as UserDomainModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Role model.
 *
 * @property positive-int                $id          ID.
 * @property string                      $name        Name.
 * @property string                      $slug        Slug.
 * @property string                      $description Description.
 * @property Carbon                      $created_at  Creation date.
 * @property Carbon                      $updated_at  Update date.
 * @property Collection<array-key, User> $users       Users.
 */
class Role extends AbstractModel
{
    use HasFactory;

    /**
     * Users this role belongs to.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class);
    }

    /**
     * Convert itself to a domain model.
     *
     * @return RoleDomainModel a new role domain model instance
     */
    public function toDomainModel(): RoleDomainModel
    {
        /** @var UserDomainModel[] */
        $userDomainModels = $this->users->map(function (User $user): UserDomainModel {
            return $user->toDomainModel();
        })->toArray();

        return new RoleDomainModel(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            description: $this->description,
            users: $userDomainModels,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): RoleFactory
    {
        return RoleFactory::new();
    }
}
