<?php

namespace App\Enums;

enum SellerProductRequestStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'قيد المراجعة',
            self::APPROVED => 'مقبول',
            self::REJECTED => 'مرفوض',
        };
    }

    public function getLabelEn(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
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
            self::APPROVED->value => self::APPROVED->getLabel(),
            self::REJECTED->value => self::REJECTED->getLabel(),
        ];
    }

    public static function getLabelsEn(): array
    {
        return [
            self::PENDING->value => self::PENDING->getLabelEn(),
            self::APPROVED->value => self::APPROVED->getLabelEn(),
            self::REJECTED->value => self::REJECTED->getLabelEn(),
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
