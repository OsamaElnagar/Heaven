@php
use App\Filament\Resources\Bookings\BookingResource;
use App\Filament\Resources\Clients\ClientResource;
use App\Filament\Resources\Expenses\ExpenseResource;
use App\Filament\Resources\ReceiptVouchers\ReceiptVoucherResource;
use App\Filament\Resources\Suppliers\SupplierResource;
use App\Filament\Resources\Trips\TripResource;
use App\Filament\Resources\Visas\VisaResource;
use Filament\Support\Colors\Color;
@endphp

<div class="flex items-center gap-1">

    <div class="w-px h-4 bg-gray-200 dark:bg-white/10"></div>

    {{-- Desktop --}}
    <div class="flex items-center gap-1" x-data x-show="window.innerWidth >= 640"
        x-on:resize.window="$el.style.display = window.innerWidth >= 640 ? 'flex' : 'none'" style="display: none">

        <x-filament::button href="{{ BookingResource::getUrl('create') }}" icon="heroicon-m-plus-circle" color="info"
            size="sm" icon-position="after" tag="a">
            حجز جديد
        </x-filament::button>

        <x-filament::button href="{{ ClientResource::getUrl('create') }}" icon="heroicon-m-user-plus" color="success"
            size="sm" icon-position="after" tag="a">
            عميل جديد
        </x-filament::button>

      
        <x-filament::button href="{{ TripResource::getUrl('create') }}" icon="heroicon-m-paper-airplane"
            :color="Color::Green" size="sm" icon-position="after" tag="a">
            رحلة جديدة
        </x-filament::button>

        <x-filament::button href="{{ ExpenseResource::getUrl('create') }}" icon="heroicon-m-receipt-refund"
            :color="Color::Orange" size="sm" icon-position="after" tag="a">
            مصروف جديد
        </x-filament::button>

        <x-filament::button href="{{ SupplierResource::getUrl('create') }}" icon="heroicon-m-building-storefront"
            :color="Color::Purple" size="sm" icon-position="after" tag="a">
            مورد جديد
        </x-filament::button>

        <x-filament::button href="{{ ReceiptVoucherResource::getUrl('create') }}" icon="heroicon-m-banknotes"
            color="warning" size="sm" icon-position="after" tag="a">
            سند قبض
        </x-filament::button>

        <x-filament::button href="{{ VisaResource::getUrl('create') }}" icon="heroicon-m-globe-alt"
            :color="Color::Cyan" size="sm" icon-position="after" tag="a">
            فيزا جديدة
        </x-filament::button>

    </div>

    {{-- Mobile --}}
    <div class="flex items-center" x-data x-show="window.innerWidth < 640"
        x-on:resize.window="$el.style.display = window.innerWidth < 640 ? 'flex' : 'none'" style="display: none">

        <x-filament::dropdown placement="bottom-start">
            <x-slot name="trigger">
                <x-filament::icon-button icon="heroicon-m-plus-circle" color="primary" size="sm" />
            </x-slot>

            <x-filament::dropdown.list>
                <x-filament::dropdown.list.item href="{{ BookingResource::getUrl('create') }}"
                    icon="heroicon-m-plus-circle" tag="a">
                    حجز جديد
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item href="{{ ClientResource::getUrl('create') }}"
                    icon="heroicon-m-user-plus" tag="a">
                    عميل جديد
                </x-filament::dropdown.list.item>

               
                <x-filament::dropdown.list.item href="{{ TripResource::getUrl('create') }}"
                    icon="heroicon-m-paper-airplane" tag="a">
                    رحلة جديدة
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item href="{{ ExpenseResource::getUrl('create') }}"
                    icon="heroicon-m-receipt-refund" tag="a">
                    مصروف جديد
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item href="{{ SupplierResource::getUrl('create') }}"
                    icon="heroicon-m-building-storefront" tag="a">
                    مورد جديد
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item href="{{ ReceiptVoucherResource::getUrl('create') }}"
                    icon="heroicon-m-banknotes" tag="a">
                    سند قبض
                </x-filament::dropdown.list.item>

                <x-filament::dropdown.list.item href="{{ VisaResource::getUrl('create') }}"
                    icon="heroicon-m-globe-alt" tag="a">
                    فيزا جديدة
                </x-filament::dropdown.list.item>
            </x-filament::dropdown.list>
        </x-filament::dropdown>

    </div>

    <div class="w-px h-4 bg-gray-200 dark:bg-white/10"></div>

</div>