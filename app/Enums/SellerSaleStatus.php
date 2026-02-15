<?php

namespace App\Enums;

enum SellerSaleStatus: int
{
    case PENDING = 1;
    case APPROVED = 2;
    case REJECTED = 3;

    /**
     * Get Arabic label for the status
     */
    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'معلقة',
            self::APPROVED => 'موافق عليها',
            self::REJECTED => 'مرفوضة',
        };
    }

    /**
     * Get English label for the status
     */
    public function getLabelEn(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
        };
    }

    /**
     * Get Bootstrap color for badge
     */
    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'warning',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
        };
    }

    /**
     * Get HTML badge for display
     */
    public function getBadgeHtml(): string
    {
        $label = app()->getLocale() === 'ar' ? $this->getLabel() : $this->getLabelEn();

        return '<span class="badge badge-' . $this->getColor() . '">' . $label . '</span>';
    }
}
