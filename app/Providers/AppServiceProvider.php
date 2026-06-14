<?php

namespace App\Providers;

use Carbon\CarbonImmutable;
use Filament\Actions\CreateAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TimePicker;
use Filament\Support\Enums\Width;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureFilament();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(
            fn (): ?Password => app()->isProduction()
                ? Password::min(12)
                    ->mixedCase()
                    ->letters()
                    ->numbers()
                    ->symbols()
                    ->uncompromised()
                : null,
        );
    }

    protected function configureFilament()
    {
        // stripped Filament tables by default
        Table::configureUsing(function (Table $table) {
            return $table
                ->striped()
                ->defaultDateDisplayFormat('Y-m-d')
                ->defaultSort('date', 'desc')
                ->defaultCurrency('egp')
                ->persistFiltersInSession()
                ->defaultPaginationPageOption(25);
        });

        DatePicker::configureUsing(fn (DatePicker $picker) => $picker->displayFormat('Y-m-d')
            ->native(false)
            ->timezone(config('app.timezone')));

        TimePicker::configureUsing(fn (TimePicker $picker) => $picker->displayFormat('H:i:s')
            ->native(false)
            ->timezone(config('app.timezone')));

        DateTimePicker::configureUsing(fn (DateTimePicker $picker) => $picker->displayFormat('Y-m-d h:m')
            ->native(false)
            ->timezone(config('app.timezone')));

        CreateAction::configureUsing(
            function (CreateAction $action) {
                return $action->slideOver()->modalWidth(Width::SevenExtraLarge);
            }
        );
        EditAction::configureUsing(
            function (EditAction $action) {
                return $action->slideOver()->modalWidth(Width::SevenExtraLarge);
            }
        );
        ViewAction::configureUsing(
            function (ViewAction $action) {
                return $action->slideOver()->modalWidth(Width::SevenExtraLarge);
            }
        );

        SelectFilter::configureUsing(
            function (SelectFilter $filter) {
                return $filter->native(false);
            }
        );

        Select::configureUsing(
            function (Select $select) {
                return $select->native(false);
            }
        );
    }
}
