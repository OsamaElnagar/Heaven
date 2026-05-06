<?php

use Illuminate\Support\Facades\Route;

Route::livewire('/', 'pages::facing.home')->name('home');

Route::livewire('/packages', 'pages::facing.packages')->name('packages.index');
Route::livewire('/packages/vip', 'pages::facing.packages.vip')->name('packages.vip');
Route::livewire('/packages/featured', 'pages::facing.packages.featured')->name('packages.featured');
Route::livewire('/packages/groups', 'pages::facing.packages.groups')->name('packages.groups');
Route::livewire('/packages/{package:slug}', 'pages::facing.packages.show')->name('packages.show');

Route::livewire('/book/{package:slug}', 'pages::facing.book')->name('book');
Route::livewire('/track', 'pages::facing.track')->name('track');
Route::livewire('/about', 'pages::facing.about')->name('about');
Route::livewire('/contact', 'pages::facing.contact')->name('contact');

Route::livewire('/news', 'pages::facing.news')->name('news.index');
Route::livewire('/news/{post:slug}', 'pages::facing.news.show')->name('news.show');
Route::livewire('/faq', 'pages::facing.faq')->name('faq');
Route::livewire('/gallery', 'pages::facing.gallery')->name('gallery');

Route::livewire('/privacy', 'pages::facing.privacy')->name('privacy');
Route::livewire('/terms', 'pages::facing.terms')->name('terms');
Route::livewire('/cancellation', 'pages::facing.cancellation')->name('cancellation');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
