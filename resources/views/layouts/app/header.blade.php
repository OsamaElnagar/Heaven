<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl" class="dark">

<head>
    @include('partials.head')
</head>

<body class="min-h-screen bg-white dark:bg-zinc-800">
    <flux:header container class="border-b border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.toggle class="lg:hidden me-2" icon="bars-2" inset="left" />

        <x-app-logo :href="route('home')" wire:navigate />

        <flux:navbar class="-mb-px max-lg:hidden">
            <flux:navbar.item :href="route('home')" :current="request()->routeIs('home')" wire:navigate>
                {{ __('الرئيسية') }}
            </flux:navbar.item>

            <flux:dropdown position="bottom" align="start">
                <flux:navbar.item icon-trailing="chevron-down" :current="request()->routeIs('packages.*')">
                    {{ __('الباقات') }}
                </flux:navbar.item>
                <flux:menu>
                    <flux:menu.item :href="route('packages.index', ['type' => 'hajj'])" wire:navigate>
                        {{ __('باقات الحج') }}
                    </flux:menu.item>
                    <flux:menu.item :href="route('packages.index', ['type' => 'umrah'])" wire:navigate>
                        {{ __('باقات العمرة') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item :href="route('packages.featured')" wire:navigate>
                        {{ __('العروض المميزة') }}
                    </flux:menu.item>
                    <flux:menu.item :href="route('packages.vip')" wire:navigate>
                        {{ __('باقات VIP') }}
                    </flux:menu.item>
                    <flux:menu.item :href="route('packages.groups')" wire:navigate>
                        {{ __('رحلات المجموعات') }}
                    </flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item :href="route('packages.index')" wire:navigate>
                        {{ __('جميع الباقات') }}
                    </flux:menu.item>
                </flux:menu>
            </flux:dropdown>

            <flux:navbar.item :href="route('track')" :current="request()->routeIs('track')" wire:navigate>
                {{ __('تتبع الحجز') }}
            </flux:navbar.item>

            <flux:navbar.item :href="route('gallery')" :current="request()->routeIs('gallery')" wire:navigate>
                {{ __('معرض الصور') }}
            </flux:navbar.item>

            <flux:navbar.item :href="route('news.index')" :current="request()->routeIs('news.*')" wire:navigate>
                {{ __('الأخبار') }}
            </flux:navbar.item>

            <flux:navbar.item :href="route('faq')" :current="request()->routeIs('faq')" wire:navigate>
                {{ __('الأسئلة الشائعة') }}
            </flux:navbar.item>

            <flux:navbar.item :href="route('about')" :current="request()->routeIs('about')" wire:navigate>
                {{ __('من نحن') }}
            </flux:navbar.item>

            <flux:navbar.item :href="route('contact')" :current="request()->routeIs('contact')" wire:navigate>
                {{ __('تواصل معنا') }}
            </flux:navbar.item>
        </flux:navbar>

        <flux:spacer />
        <flux:button x-data x-on:click="$flux.dark = ! $flux.dark" icon="moon" variant="subtle" class="mx-1"
            aria-label="Toggle dark mode" />
        @auth
            <x-desktop-user-menu />
        @else
            <flux:button :href="route('login')" wire:navigate variant="outline" size="sm">
                {{ __('Login') }}
            </flux:button>
        @endauth
    </flux:header>

    <!-- Mobile Menu -->
    <flux:sidebar collapsible="mobile" sticky
        class="lg:hidden border-e border-zinc-200 bg-zinc-50 dark:border-zinc-700 dark:bg-zinc-900">
        <flux:sidebar.header>
            <x-app-logo :sidebar="true" href="{{ auth()->check() ? route('dashboard') : route('home') }}"
                wire:navigate />
            <flux:sidebar.collapse
                class="in-data-flux-sidebar-on-desktop:not-in-data-flux-sidebar-collapsed-desktop:-mr-2" />
        </flux:sidebar.header>

        <flux:sidebar.nav>

            <flux:sidebar.group :heading="__('القائمة الرئيسية')">
                <flux:sidebar.item icon="home" :href="route('home')" :current="request()->routeIs('home')"
                    wire:navigate>
                    {{ __('الرئيسية') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="globe-alt" :href="route('packages.index')"
                    :current="request()->routeIs('packages.index')" wire:navigate>
                    {{ __('جميع الباقات') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="star" :href="route('packages.featured')"
                    :current="request()->routeIs('packages.featured')" wire:navigate>
                    {{ __('العروض المميزة') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="sparkles" :href="route('packages.vip')"
                    :current="request()->routeIs('packages.vip')" wire:navigate>
                    {{ __('باقات VIP') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="user-group" :href="route('packages.groups')"
                    :current="request()->routeIs('packages.groups')" wire:navigate>
                    {{ __('رحلات المجموعات') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

            <flux:sidebar.group :heading="__('خدماتنا')">
                <flux:sidebar.item icon="magnifying-glass" :href="route('track')"
                    :current="request()->routeIs('track')" wire:navigate>
                    {{ __('تتبع الحجز') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="question-mark-circle" :href="route('faq')"
                    :current="request()->routeIs('faq')" wire:navigate>
                    {{ __('الأسئلة الشائعة') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="photo" :href="route('gallery')" :current="request()->routeIs('gallery')"
                    wire:navigate>
                    {{ __('معرض الصور') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="newspaper" :href="route('news.index')"
                    :current="request()->routeIs('news.*')" wire:navigate>
                    {{ __('الأخبار') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="information-circle" :href="route('about')"
                    :current="request()->routeIs('about')" wire:navigate>
                    {{ __('من نحن') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="phone" :href="route('contact')" :current="request()->routeIs('contact')"
                    wire:navigate>
                    {{ __('تواصل معنا') }}
                </flux:sidebar.item>
            </flux:sidebar.group>

        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
            @auth
                <flux:sidebar.item icon="cog-6-tooth" :href="route('profile.edit')" wire:navigate>
                    {{ __('Settings') }}
                </flux:sidebar.item>
                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:sidebar.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer">
                        {{ __('Log out') }}
                    </flux:sidebar.item>
                </form>
            @else
                <flux:sidebar.item icon="arrow-right-end-on-rectangle" :href="route('login')" wire:navigate>
                    {{ __('Login') }}
                </flux:sidebar.item>
            @endauth
        </flux:sidebar.nav>
    </flux:sidebar>

    {{ $slot }}

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
