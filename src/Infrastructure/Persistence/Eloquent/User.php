<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Database\Factories\UserFactory;
use Domain\Model\User\Sex;
use Domain\Model\User\User as UserDomainModel;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;

/**
 * User model.
 *
 * @property positive-int                $id         ID.
 * @property string                      $name       Name.
 * @property string                      $email      Email address.
 * @property int<0, max>                 $age        Age.
 * @property string                      $sex        Sex.
 * @property Carbon                      $created_at Creation date.
 * @property Carbon                      $updated_at Update date.
 * @property Collection<array-key, Role> $roles      Roles.
 */
class User extends AbstractModel
{
    use HasFactory;

    public const SEX_MALE = 'male';

    public const SEX_FEMALE = 'female';

    /**
     * Roles this user belongs to.
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class);
    }

    /**
     * Convert itself to a domain model.
     *
     * @return UserDomainModel a new user domain model instance
     */
    public function toDomainModel(): UserDomainModel
    {
        /** @var positive-int[] */
        $roleIds = $this->roles->pluck('id')->toArray();

        return new UserDomainModel(
            id: $this->id,
            name: $this->name,
            email: $this->email,
            age: $this->age,
            sex: Sex::from($this->sex),
            roleIds: $roleIds,
        );
    }

    /**
     * {@inheritdoc}
     */
    protected static function newFactory(): UserFactory
    {
        return UserFactory::new();
    }
}
