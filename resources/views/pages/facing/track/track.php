<?php

use App\Models\Booking;
use Livewire\Attributes\Title;
use Livewire\Component;

new
#[Title('تتبع الحجز')]
class extends Component
{
    public string $reference = '';

    public string $nationalId = '';

    public ?Booking $booking = null;

    public bool $searched = false;

    public ?string $error = null;

    public function track(): void
    {
        $this->validate([
            'reference' => ['required', 'string'],
            'nationalId' => ['required', 'string'],
        ]);

        $booking = Booking::where('reference', $this->reference)
            ->whereHas('client', fn ($q) => $q->where('national_id', $this->nationalId))
            ->with(['package', 'client', 'payments', 'visa'])
            ->first();

        $this->searched = true;

        if (! $booking) {
            $this->booking = null;
            $this->error = 'لم يتم العثور على حجز بهذه البيانات. يرجى التحقق من رقم الحجز والرقم القومي.';

            return;
        }

        $this->booking = $booking;
        $this->error = null;
    }

    public function getRemainingProperty(): float
    {
        if (! $this->booking) {
            return 0;
        }

        return max(0, $this->booking->net_price - $this->booking->paid_amount);
    }
};
