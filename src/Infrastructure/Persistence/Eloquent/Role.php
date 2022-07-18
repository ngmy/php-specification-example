<?php

declare(strict_types=1);

namespace Infrastructure\Persistence\Eloquent;

use Database\Factories\RoleFactory;
use Domain\Model\Role\Role as RoleDomainModel;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Role model.
 *
 * @property positive-int $id          ID.
 * @property string       $name        Name.
 * @property string       $slug        Slug.
 * @property string       $description Description.
 * @property Carbon       $created_at  Creation date.
 * @property Carbon       $updated_at  Update date.
 */
class Role extends AbstractModel
{
    use HasFactory;

    public const NAME_ADMIN = '管理者';

    public const NAME_USER = 'ユーザー';

    public const SLUG_ADMIN = 'admin';

    public const SLUG_USER = 'user';

    /**
     * Convert itself to a domain model.
     *
     * @return RoleDomainModel a new role domain model instance
     */
    public function toDomainModel(): RoleDomainModel
    {
        return new RoleDomainModel(
            id: $this->id,
            name: $this->name,
            slug: $this->slug,
            description: $this->description,
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
