<?php

namespace App\Enums;

enum OrderStatus: int
{
    case PENDING = 1;
    case DONE = 2;
    case CANCELLED = 3;
    case REFUNDED = 4;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'قيد التنفيذ',
            self::DONE => 'تم التنفيذ',
            self::CANCELLED => 'ملغي',
            self::REFUNDED => 'مسترد',
        };
    }

    public function getLabelEn(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::DONE => 'Done',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'info',
            self::DONE => 'success',
            self::CANCELLED => 'danger',
            self::REFUNDED => 'warning',
        };
    }

    public function getBadgeHtml(): string
    {
        return '<span class="badge bg-' . $this->getColor() . '">' . $this->getLabelLocalized() . '</span>';
    }

    public function getLabelLocalized(): string
    {
        return app()->getLocale() === 'ar' ? $this->getLabel() : $this->getLabelEn();
    }

    public static function getLabels(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabel(),
            self::DONE->value => self::DONE->getLabel(),
            self::CANCELLED->value => self::CANCELLED->getLabel(),
            self::REFUNDED->value => self::REFUNDED->getLabel(),
        ];
    }

    public static function getLabelsEn(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabelEn(),
            self::DONE->value => self::DONE->getLabelEn(),
            self::CANCELLED->value => self::CANCELLED->getLabelEn(),
            self::REFUNDED->value => self::REFUNDED->getLabelEn(),
        ];
    }

    public static function getLabelsLocalized(): array
    {
        return app()->getLocale() === 'ar' ? self::getLabels() : self::getLabelsEn();
    }

    public static function getOptions(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'label' => $case->getLabelLocalized(),
            'color' => $case->getColor(),
        ], self::cases());
    }
}
