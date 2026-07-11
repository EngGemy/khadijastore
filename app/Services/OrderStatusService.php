<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use InvalidArgumentException;

class OrderStatusService
{
    /** @var array<string, list<string>> */
    public const TRANSITIONS = [
        'pending' => ['confirmed', 'cancelled'],
        'confirmed' => ['processing', 'cancelled'],
        'processing' => ['shipped', 'cancelled'],
        'shipped' => ['delivered', 'cancelled'],
        'delivered' => [],
        'cancelled' => [],
    ];

    public function canTransition(string $from, string $to): bool
    {
        return in_array($to, self::TRANSITIONS[$from] ?? [], true);
    }

    /** @return list<string> */
    public function nextStatuses(Order $order): array
    {
        return self::TRANSITIONS[$order->status] ?? [];
    }

    public function transition(Order $order, string $toStatus, ?string $note = null, ?User $user = null): void
    {
        if (! array_key_exists($toStatus, Order::STATUSES)) {
            throw new InvalidArgumentException("Invalid status: {$toStatus}");
        }

        if ($order->status === $toStatus) {
            return;
        }

        if (! $this->canTransition($order->status, $toStatus)) {
            throw new InvalidArgumentException("Cannot transition from {$order->status} to {$toStatus}");
        }

        $data = [
            'status' => $toStatus,
            'handled_by' => $user?->id ?? auth()->id(),
        ];

        if ($toStatus === 'confirmed' && ! $order->confirmed_at) {
            $data['confirmed_at'] = now();
        }

        $order->statusChangeNote = $note;
        $order->update($data);
        $order->statusChangeNote = null;
    }
}
