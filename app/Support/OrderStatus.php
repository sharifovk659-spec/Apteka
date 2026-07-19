<?php

namespace App\Support;

class OrderStatus
{
    public const NEW = 'new';

    public const CONFIRMED = 'confirmed';

    public const PROCESSING = 'processing';

    public const DELIVERING = 'delivering';

    public const COMPLETED = 'completed';

    public const CANCELLED = 'cancelled';

    public static function all(): array
    {
        return [
            self::NEW,
            self::CONFIRMED,
            self::PROCESSING,
            self::DELIVERING,
            self::COMPLETED,
            self::CANCELLED,
        ];
    }

    public static function labels(): array
    {
        return [
            self::NEW => 'Новый',
            self::CONFIRMED => 'Подтверждён',
            self::PROCESSING => 'В обработке',
            self::DELIVERING => 'Доставляется',
            self::COMPLETED => 'Завершён',
            self::CANCELLED => 'Отменён',
        ];
    }

    public static function label(string $status): string
    {
        return self::labels()[$status] ?? $status;
    }

    public static function cancellable(string $status): bool
    {
        return ! in_array($status, [self::COMPLETED, self::CANCELLED], true);
    }
}
