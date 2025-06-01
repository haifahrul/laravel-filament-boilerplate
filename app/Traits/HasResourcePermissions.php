<?php

namespace App\Traits;

trait HasResourcePermissions
{
  protected static function getPermissionPrefix(): string
  {
    // Ambil nama resource dari class, misal: UserResource → user
    return str(class_basename(static::class))
      ->before('Resource')
      ->plural()
      ->lower()
      ->value();
  }

  public static function canViewAny(): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.view');
  }

  public static function canCreate(): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.create');
  }

  public static function canEdit($record): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.update');
  }

  public static function canDelete($record): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.delete');
  }

  public static function canDeleteAny(): bool
  {
    return static::canDelete(null);
  }

  public static function canRestore($record): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.restore');
  }

  public static function canForceDelete($record): bool
  {
    return auth()->user()?->can(static::getPermissionPrefix() . '.force_delete');
  }
}