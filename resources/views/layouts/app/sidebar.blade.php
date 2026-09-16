<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">

<head>
    @include('partials.head')
    <style>
        .nfl-sidebar {
            background-color: #8F1D2C !important;
            border-color: rgba(255, 255, 255, 0.12) !important;
        }

        .dashboard-card {
            background-color: #123B63 !important;
        }

        .nfl-sidebar .text-zinc-400 {
            color: white !important;
        }

        .nfl-sidebar [data-flux-sidebar-item] {
            color: white !important;
            font-size: 16px !important;
            min-height: 46px !important;
        }

        .nfl-sidebar [data-flux-sidebar-item] svg {
            width: 20px !important;
            height: 20px !important;
        }

        .dashboard-card a {
            color: #123B63 !important;
        }

        .nfl-sidebar [data-flux-sidebar-item][data-current] {
            color: #123B63 !important;
            background-color: white !important;
        }

        .nfl-sidebar [data-flux-sidebar-profile]>div+span {
            color: white !important;
        }

        .standings-page {
            background-color: #013368 !important;
            border: 2px solid white !important;
        }

        .standings-player {
            background-color: white !important;
            border: 2px solid #D62027 !important;
            outline: 2px solid white !important;
            outline-offset: 2px;
        }

        .standings-player p {
            color: #1E293B !important;
        }

        .standings-player p.text-\[\#475569\] {
            color: #475569 !important;
        }

        .standings-list {
            width: 100% !important;
            max-width: 700px !important;
            margin-left: auto !important;
            margin-right: auto !important;
        }
    </style>
</head>

<body class="min-h-screen bg-white dark:bg-white">
    <flux:sidebar sticky collapsible="mobile" class="nfl-sidebar">
        <flux:sidebar.header>

        </flux:sidebar.header>

        <flux:sidebar.nav>
            <flux:sidebar.group :heading="__('NFL - Pick`em all')" class="grid" style="margin-top: 65px;">
                <flux:sidebar.item icon="home" :href="route('dashboard')" :current="request()->routeIs('dashboard')"
                    wire:navigate>
                    {{ __('Inicio') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="calendar-days" :href="route('rounds')" :current="request()->routeIs('rounds*')"
                    wire:navigate>
                    {{ __('Jornadas') }}
                </flux:sidebar.item>

                <flux:sidebar.item icon="trophy" :href="route('standings')" :current="request()->routeIs('standings')"
                    wire:navigate>
                    {{ __('Clasificación') }}
                </flux:sidebar.item>

                @if (auth()->user()->is_admin)
                    <flux:sidebar.group :heading="__('Administración')" class="grid">
                        <flux:sidebar.item icon="shield-check" :href="route('admin.rounds')"
                            :current="request()->routeIs('admin.rounds*')" wire:navigate>
                            {{ __('Jornadas') }}
                        </flux:sidebar.item>
                    </flux:sidebar.group>
                @endif
            </flux:sidebar.group>
        </flux:sidebar.nav>

        <flux:spacer />

        <flux:sidebar.nav>
        </flux:sidebar.nav>

        <x-desktop-user-menu class="hidden lg:block" :name="auth()->user()->name" />
    </flux:sidebar>

    <!-- App Header -->
    <flux:header class="hidden lg:flex"
        style="
        height: 64px;
        background: #123B63;
        border-bottom: 3px solid #8F1D2C;
    ">
        <div
            style="
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 24px;
        ">
            <span
                style="
                font-size: 25px;
                line-height: 1;
                font-weight: 900;
                letter-spacing: -0.03em;
                color: white;
            ">
                NFL
            </span>

            <span
                style="
                width: 3px;
                height: 25px;
                background: #D62027;
                transform: skewX(-12deg);
            "></span>

            <span
                style="
                font-size: 16px;
                line-height: 1;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: white;
            ">
                PICK'EM ALL
            </span>
        </div>

        <flux:spacer />

        <div style="padding-right: 24px;">
            <span
                style="
                font-size: 13px;
                font-weight: 700;
                color: rgba(255,255,255,0.85);
            ">
                {{ auth()->user()->name }}
            </span>
        </div>
    </flux:header>
    <!-- App Header -->
    <flux:header class="hidden lg:flex"
        style="
        height: 64px;
        background: #123B63;
        border-bottom: 3px solid #8F1D2C;
    ">
        <div
            style="
            display: flex;
            align-items: center;
            gap: 12px;
            padding-left: 120px;
        ">
            <span
                style="
                font-size: 25px;
                line-height: 1;
                font-weight: 900;
                letter-spacing: -0.03em;
                color: white;
            ">
                NFL
            </span>

            <span
                style="
                width: 3px;
                height: 25px;
                background: #D62027;
                transform: skewX(-12deg);
            "></span>

            <span
                style="
                font-size: 16px;
                line-height: 1;
                font-weight: 800;
                letter-spacing: 0.08em;
                text-transform: uppercase;
                color: white;
            ">
                PICK'EM ALL
            </span>
        </div>

        <flux:spacer />

        <div style="
        padding-right: 120px;
    ">
            <span
                style="
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.85);
        ">
                PICK <span style="color: #D62027;">•</span> PLAY <span style="color: #D62027;">•</span> WIN
            </span>
        </div>
    </flux:header>

    <!-- Mobile User Menu -->
    <flux:header class="lg:hidden">
        <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

        <flux:spacer />

        <flux:dropdown position="top" align="end">
            <flux:profile :initials="auth()->user()->initials()" icon-trailing="chevron-down" />

            <flux:menu>
                <flux:menu.radio.group>
                    <div class="p-0 text-sm font-normal">
                        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                            <flux:avatar :name="auth()->user()->name" :initials="auth()->user()->initials()" />

                            <div class="grid flex-1 text-start text-sm leading-tight">
                                <flux:heading class="truncate">{{ auth()->user()->name }}</flux:heading>
                                <flux:text class="truncate">{{ auth()->user()->email }}</flux:text>
                            </div>
                        </div>
                    </div>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <flux:menu.radio.group>
                    <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                        {{ __('Settings') }}
                    </flux:menu.item>
                </flux:menu.radio.group>

                <flux:menu.separator />

                <form method="POST" action="{{ route('logout') }}" class="w-full">
                    @csrf
                    <flux:menu.item as="button" type="submit" icon="arrow-right-start-on-rectangle"
                        class="w-full cursor-pointer" data-test="logout-button">
                        {{ __('Log out') }}
                    </flux:menu.item>
                </form>
            </flux:menu>
        </flux:dropdown>
    </flux:header>

    {{ $slot }}

    @persist('toast')
        <flux:toast.group>
            <flux:toast />
        </flux:toast.group>
    @endpersist

    @fluxScripts
</body>

</html>
