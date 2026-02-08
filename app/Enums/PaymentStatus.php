<?php

namespace App\Enums;

enum PaymentStatus: int
{
    case PAID = 1;
    case UNPAID = 2;

    public function getLabel(): string
    {
        return match ($this) {
            self::PAID => 'مدفوع',
            self::UNPAID => 'غير مدفوع',
        };
    }

    public function getLabelEn(): string
    {
        return match ($this) {
            self::PAID => 'Paid',
            self::UNPAID => 'Unpaid',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PAID => 'success',
            self::UNPAID => 'warning',
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
            self::PAID->value => self::PAID->getLabel(),
            self::UNPAID->value => self::UNPAID->getLabel(),
        ];
    }

    public static function getLabelsEn(): array
    {
        return [
            self::PAID->value => self::PAID->getLabelEn(),
            self::UNPAID->value => self::UNPAID->getLabelEn(),
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
