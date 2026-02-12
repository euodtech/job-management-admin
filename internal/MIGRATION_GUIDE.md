# E-FMS Internal Dashboard: Tailwind CSS + Preline UI Migration Guide

> **Purpose**: This standalone document enables a fresh LLM conversation (with no prior context) to execute a complete migration from AdminLTE 3.1.0 + Bootstrap 4 to Tailwind CSS v3 + Preline UI v2.x, file-by-file.
>
> **Project path**: `C:\xampp\htdocs\be-fms\internal\`
> **Views path**: `internal/application/views/`
> **Assets path**: `internal/assets/`
> **Runtime**: XAMPP on Windows (PHP 7.1+, Apache, no Node.js)

---

## Table of Contents

1. [Setup Instructions](#1-setup-instructions)
2. [CSS Variables & Tailwind Config](#2-css-variables--tailwind-config)
3. [Component-to-Preline Mapping Table](#3-component-to-preline-mapping-table)
4. [File-by-File Conversion Order](#4-file-by-file-conversion-order)
5. [JS Dependencies Preservation Guide](#5-js-dependencies-preservation-guide)
6. [Dark Mode Setup](#6-dark-mode-setup)
7. [Edge Cases & Gotchas](#7-edge-cases--gotchas)
8. [Test Plan](#8-test-plan)

---

## 1. Setup Instructions

### 1.1 Add Tailwind CSS v3 CDN (Play CDN)

In `main/header.php`, inside `<head>`, **replace** the AdminLTE CSS link and Google Fonts link with:

```html
<!-- Tailwind CSS v3 CDN Play Script -->
<script src="https://cdn.tailwindcss.com"></script>

<!-- Preline UI v2 CSS (none needed - it uses Tailwind utilities) -->

<!-- Inter font (replaces Source Sans Pro) -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">
```

### 1.2 Add Preline UI JS

At the **very bottom** of `main/footer.php`, just before `</body>`, add:

```html
<!-- Preline UI v2 -->
<script src="https://cdn.jsdelivr.net/npm/preline@2/dist/preline.min.js"></script>

<!-- Re-init Preline after AJAX/dynamic content -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
</script>
```

### 1.3 Tailwind Config (inline in header.php)

Place this **immediately after** the Tailwind CDN `<script>` tag in `main/header.php`:

```html
<script>
tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Source Sans Pro', 'sans-serif'],
            },
            colors: {
                primary: {
                    DEFAULT: '#070f26',
                    dark: '#0a1431',
                    light: '#1a2744',
                },
                accent: {
                    DEFAULT: '#4e54c8',
                    light: '#8f94fb',
                },
                destructive: '#da1e26',
                surface: '#ffffff',
                background: '#f4f6f9',
                muted: '#6c757d',
            },
        },
    },
}
</script>
```

### 1.4 CSS Variables `:root` Block

Add this `<style>` block in `main/header.php` (replaces the existing AdminLTE override `<style>`):

```html
<style>
:root {
    --color-primary: #070f26;
    --color-primary-dark: #0a1431;
    --color-primary-light: #1a2744;
    --color-secondary: #6c757d;
    --color-accent: #4e54c8;
    --color-accent-light: #8f94fb;
    --color-background: #f4f6f9;
    --color-surface: #ffffff;
    --color-text: #212529;
    --color-muted: #6c757d;
    --color-destructive: #da1e26;
    --color-success: #28a745;
    --color-warning: #ffc107;
    --color-info: #17a2b8;
}

/* === KEEP: Status badges (used in DataTables JS renders) === */
.ongoing_job {
    @apply inline-block whitespace-nowrap rounded-full bg-amber-100 px-2.5 py-0.5 text-sm font-semibold text-amber-800;
}
.finished_job {
    @apply inline-block whitespace-nowrap rounded-full bg-green-100 px-2.5 py-0.5 text-sm font-semibold text-green-800;
}
.awaiting_job {
    @apply inline-block whitespace-nowrap rounded-full bg-red-100 px-2.5 py-0.5 text-sm font-semibold text-red-800;
}
.reschedule_job {
    @apply inline-block whitespace-nowrap rounded-full bg-blue-100 px-2.5 py-0.5 text-sm font-semibold text-blue-800;
}
.completed_job {
    @apply inline-block whitespace-nowrap rounded-full bg-green-500 px-2.5 py-0.5 text-sm font-semibold text-white shadow-md shadow-green-500/40;
}

/* === KEEP: Loading blur overlay === */
.blur { filter: blur(20px); }

/* === KEEP: Bell shake animation (sidebar notification) === */
.bell-shake { animation: shake 0.5s infinite; }
@keyframes shake {
    0%   { transform: rotate(0deg); }
    25%  { transform: rotate(-15deg); }
    50%  { transform: rotate(15deg); }
    75%  { transform: rotate(-15deg); }
    100% { transform: rotate(0deg); }
}

/* === KEEP: Alert auto-dismiss animation === */
.alert-auto-dismiss {
    animation: fadeInOut 6s forwards;
}
@keyframes fadeInOut {
    0%   { opacity: 0; transform: translateY(-30px); }
    20%  { opacity: 1; transform: translateY(0); }
    70%  { opacity: 1; transform: translateY(0); }
    100% { opacity: 0; transform: translateY(-30px); }
}

/* === DataTables overrides for Tailwind === */
table.dataTable { @apply w-full text-sm; }
table.dataTable thead th { @apply bg-gray-50 px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-600 border-b border-gray-200; }
table.dataTable tbody td { @apply px-4 py-3 border-b border-gray-100; }
table.dataTable tbody tr:hover { @apply bg-gray-50; }
.dataTables_wrapper .dataTables_length select { @apply rounded-md border-gray-300 text-sm py-1 px-2; }
.dataTables_wrapper .dataTables_filter input { @apply rounded-md border-gray-300 text-sm py-1.5 px-3 focus:ring-2 focus:ring-primary/20 focus:border-primary; }
.dataTables_wrapper .dataTables_info { @apply text-sm text-gray-600 py-3; }
.dataTables_wrapper .dataTables_paginate .paginate_button { @apply px-3 py-1.5 text-sm rounded-md mx-0.5; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current { @apply bg-primary text-white; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.current) { @apply bg-gray-100; }
div.dataTables_wrapper div.dataTables_processing { @apply bg-white/80 rounded-lg; }
.dt-buttons .dt-button { @apply inline-flex items-center gap-1.5 rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors; }

/* === Select2 overrides for Tailwind === */
.select2-container--bootstrap4 .select2-selection--single { @apply rounded-lg border-gray-300 h-10 flex items-center px-3 text-sm; }
.select2-container--bootstrap4 .select2-selection--single .select2-selection__arrow { @apply h-10 flex items-center; }
.select2-container--bootstrap4 .select2-results__option--highlighted { @apply bg-primary text-white; }
.select2-dropdown { @apply rounded-lg border border-gray-200 shadow-lg; }

/* === Form validation error state === */
.field-error .tw-input,
.field-error input,
.field-error select,
.field-error textarea,
.field-error .select2-selection { @apply !border-red-500; }
.field-error .input-group-text-tw { @apply !border-red-500; }
.inline-error { @apply text-red-500 text-xs mt-1 block; }

/* === Print hide === */
@media print {
    .no-print { display: none !important; }
}

/* === Mobile DataTables adjustments === */
@media (max-width: 991.98px) {
    .dataTables_info, .dataTables_paginate { font-size: 12px !important; margin-top: 0.75rem !important; }
    .dataTables_length, .dataTables_filter { font-size: 12px !important; }
}
</style>
```

### 1.5 What to REMOVE from header.php

Remove these CSS links (no longer needed):
```
- adminlte.min.css
- tempusdominus-bootstrap-4.min.css
- Google Font Source Sans Pro (replaced by Inter)
- icheck-bootstrap (not used in Tailwind)
- overlayScrollbars CSS
```

**KEEP** these CSS links (still needed):
```
+ Leaflet CSS (leaflet@1.9.4)
+ Leaflet Control Geocoder CSS
+ DataTables BS4 CSS (dataTables.bootstrap4.min.css) -- KEEP until overrides are stable
+ DataTables Responsive CSS
+ DataTables Buttons CSS
+ Select2 CSS + Select2 Bootstrap4 theme
+ Summernote BS4 CSS
+ OpenLayers CSS
+ Font Awesome 7 CSS (all links)
+ Ionicons CSS
+ map.min.css
```

### 1.6 What to REMOVE from footer.php

Remove these JS files (no longer needed):
```
- adminlte.js (replaced by Preline for sidebar/collapse/treeview)
- bootstrap.bundle.min.js (replaced by Preline for modals/dropdowns/collapse)
```

> **IMPORTANT**: Bootstrap JS removal must happen AFTER all `data-toggle="modal"` and `data-dismiss="modal"` attributes are converted to Preline `data-hs-overlay`. Do this in Phase A when converting header/footer.

**KEEP** these JS files:
```
+ jQuery 3.6.0 (MUST KEEP - used by DataTables, Select2, Summernote, all custom JS)
+ jQuery UI (widget.bridge for button)
+ Select2
+ Summernote
+ DataTables (full suite: core, BS4, responsive, buttons)
+ JSZip, PDFMake, vfs_fonts
+ Moment.js
+ SweetAlert2
+ Chart.js (loaded per-page)
+ Leaflet + Geocoder (loaded per-page)
+ OpenLayers (loaded in header)
```

---

## 2. CSS Variables & Tailwind Config

### 2.1 Color Token Reference

| Token | Hex | Tailwind Class | Usage |
|-------|-----|---------------|-------|
| primary | `#070f26` | `bg-primary`, `text-primary` | Topbar, sidebar active, buttons |
| primary-dark | `#0a1431` | `bg-primary-dark` | Info-box icons, gradients |
| primary-light | `#1a2744` | `bg-primary-light` | Hover states |
| accent | `#4e54c8` | `bg-accent` | Gradient start |
| accent-light | `#8f94fb` | `bg-accent-light` | Gradient end |
| destructive | `#da1e26` | `bg-destructive` | Delete/danger buttons |
| background | `#f4f6f9` | `bg-background` | Page background |
| surface | `#ffffff` | `bg-surface` | Cards, modals |
| muted | `#6c757d` | `text-muted` | Secondary text |

### 2.2 Gradient Utility

The `btn-primary-gradient` class used on some buttons:

```css
/* Add to the <style> block */
.btn-gradient-primary {
    @apply bg-gradient-to-r from-primary-dark to-black text-white;
    background-size: 200% 100%;
    background-position: left center;
    transition: background-position 1s ease;
}
.btn-gradient-primary:hover {
    background-position: right center;
}
.btn-gradient-accent {
    @apply bg-gradient-to-br from-accent to-accent-light text-white;
}
```

---

## 3. Component-to-Preline Mapping Table

### 3.1 Layout Components

| AdminLTE/Bootstrap | Tailwind + Preline Equivalent |
|---|---|
| `<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">` | `<body class="bg-background font-sans text-gray-800">` |
| `<div class="wrapper">` | `<div class="min-h-screen flex flex-col">` |
| `<div class="content-wrapper">` | `<main class="lg:ml-64 pt-16 pb-16 min-h-screen transition-all duration-300">` |
| `<section class="content-header">` | `<div class="px-4 sm:px-6 lg:px-8 py-4">` |
| `<section class="content">` | `<div class="px-4 sm:px-6 lg:px-8 pb-6">` |
| `<div class="container-fluid">` | `<div class="max-w-full">` (or just remove, Tailwind padding handles it) |
| `<footer class="main-footer">` | `<footer class="lg:ml-64 border-t border-gray-200 bg-white px-6 py-3 text-sm text-gray-500 no-print">` |

### 3.2 Topbar / Navbar

| AdminLTE | Tailwind + Preline |
|---|---|
| `<nav class="main-header navbar navbar-expand navbar-dark" style="background-color: #070f26;">` | `<header class="fixed top-0 left-0 right-0 z-50 flex items-center justify-between bg-primary px-4 h-16 lg:ml-64 transition-all duration-300">` |
| `<a data-widget="pushmenu">` (hamburger) | `<button type="button" data-hs-overlay="#sidebar" class="lg:hidden text-white">` |
| `<i class="fa fa-bars" style="color: white;">` | `<i class="fa fa-bars text-white text-lg"></i>` |
| Sign out link with inline styles | `<a href="..." class="flex items-center gap-2 text-white font-bold hover:text-gray-200 transition-colors">` |

### 3.3 Sidebar

| AdminLTE | Tailwind + Preline |
|---|---|
| `<aside class="main-sidebar sidebar-light-navy elevation-1">` | See full sidebar template below |
| `<a class="brand-link">` | `<div class="flex items-center justify-center gap-2 h-16 border-b border-gray-200 bg-white px-4">` |
| `<div class="sidebar">` | `<nav class="flex-1 overflow-y-auto p-4">` |
| `<div class="user-panel mt-3 pb-3 mb-3 d-flex">` | `<div class="flex items-center gap-3 px-2 py-3 mb-4 border-b border-gray-200">` |
| `<ul class="nav nav-pills nav-sidebar flex-column">` | `<ul class="space-y-1">` |
| `<li class="nav-header pl-3">MASTER</li>` | `<li class="px-3 pt-4 pb-1 text-xs font-semibold uppercase tracking-wider text-gray-400">MASTER</li>` |
| `<li class="nav-item"><a class="nav-link active">` | `<li><a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium bg-primary text-white">` |
| `<li class="nav-item"><a class="nav-link">` (inactive) | `<li><a class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors">` |
| `<li class="nav-item has-treeview">` + `data-widget="treeview"` | Preline Accordion: `<div class="hs-accordion" id="sidebar-accordion-job">` |
| `<ul class="nav nav-treeview">` | Accordion body: `<div id="sidebar-accordion-job-child" class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300" role="region">` |
| `<i class="right fas fa-angle-left"></i>` (treeview toggle icon) | `<svg class="hs-accordion-active:rotate-180 ...">` (auto-rotates) |
| `<i class="nav-icon far fa-circle">` (submenu bullet) | `<span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>` (or `<i class="nav-icon far fa-circle text-xs">`) |
| `<i class="nav-icon fas fa-check-circle">` (active submenu) | `<span class="w-1.5 h-1.5 rounded-full bg-primary"></span>` |

**Full Sidebar Template (Preline Offcanvas):**

```html
<!-- Sidebar Overlay for Mobile -->
<div id="sidebar"
     class="hs-overlay [--auto-close:lg] hs-overlay-open:translate-x-0
            -translate-x-full fixed top-0 left-0 z-[60] w-64 h-full
            bg-white border-r border-gray-200
            transition-transform duration-300
            lg:translate-x-0 lg:z-40"
     role="dialog" tabindex="-1">

    <!-- Brand Logo -->
    <div class="flex items-center justify-center gap-2 h-16 border-b border-gray-200 bg-white px-4">
        <img src="<?= $brandLogo ?>" class="h-8 object-contain" alt="Logo">
        <span class="text-sm font-extrabold text-gray-800 truncate">fms | Administrator</span>
    </div>

    <!-- User Panel -->
    <div class="flex items-center gap-3 px-4 py-3 border-b border-gray-100">
        <img src="<?= base_url('assets/dist/1144760.png') ?>" class="w-9 h-9 rounded-full ring-2 ring-gray-200" alt="User">
        <span class="text-sm font-semibold text-gray-700 truncate"><?= strtoupper($this->session->userdata('Fullname')) ?></span>
    </div>

    <!-- Sidebar Menu -->
    <nav class="flex-1 overflow-y-auto p-4">
        <!-- Section: MASTER -->
        <p class="px-3 pt-2 pb-2 text-xs font-semibold uppercase tracking-wider text-gray-400">Master</p>
        <ul class="space-y-1">
            <!-- Dashboard -->
            <li>
                <a href="<?= base_url('home') ?>"
                   class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium
                          <?= $this->uri->segment('1') == 'home' ? 'bg-primary text-white' : 'text-gray-700 hover:bg-gray-100' ?>
                          transition-colors">
                    <i class="fa-solid fa-chart-line w-5 text-center"></i>
                    Dashboard
                </a>
            </li>
            <!-- ... other nav items follow same pattern ... -->
        </ul>

        <!-- Section: Job List (Accordion/Treeview) -->
        <div class="hs-accordion-group mt-2" data-hs-accordion-always-open>
            <div class="hs-accordion <?= (in_array($this->uri->segment('1'), ['line-interruption-job','short-circuit-job','disconnection-job','reconnection-job'])) ? 'active' : '' ?>"
                 id="sidebar-accordion-job">
                <button type="button"
                        class="hs-accordion-toggle flex items-center justify-between w-full rounded-lg px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 transition-colors
                               hs-accordion-active:bg-primary hs-accordion-active:text-white">
                    <span class="flex items-center gap-3">
                        <i class="fa-solid fa-briefcase w-5 text-center"></i>
                        Job List
                        <span class="text-xs font-bold count_notif_sidebar"></span>
                    </span>
                    <svg class="hs-accordion-active:rotate-180 w-4 h-4 transition-transform" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div id="sidebar-accordion-job-child"
                     class="hs-accordion-content hidden w-full overflow-hidden transition-[height] duration-300"
                     role="region" aria-labelledby="sidebar-accordion-job">
                    <ul class="pl-7 space-y-1 mt-1">
                        <li>
                            <a href="<?= base_url('line-interruption-job') ?>"
                               class="flex items-center gap-2 rounded-lg px-3 py-1.5 text-sm
                                      <?= ($this->uri->segment(1) == 'line-interruption-job') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-gray-900' ?>">
                                <span class="w-1.5 h-1.5 rounded-full <?= ($this->uri->segment(1) == 'line-interruption-job') ? 'bg-primary' : 'bg-gray-400' ?>"></span>
                                Line Interruption
                                <span class="text-xs font-bold count_line"></span>
                            </a>
                        </li>
                        <!-- Short Circuit, Disconnection, Reconnection follow same pattern -->
                    </ul>
                </div>
            </div>
        </div>
        <!-- ... Report accordion follows same pattern ... -->
    </nav>
</div>
```

### 3.4 Cards

| AdminLTE | Tailwind |
|---|---|
| `<div class="card">` | `<div class="bg-white rounded-xl shadow-sm border border-gray-200">` |
| `<div class="card-header">` | `<div class="px-5 py-4 border-b border-gray-200">` |
| `<h3 class="card-title">` | `<h3 class="text-base font-semibold text-gray-800">` |
| `<div class="card-tools">` | `<div class="flex items-center gap-2">` |
| `<button class="btn btn-tool" data-card-widget="collapse">` | `<button type="button" data-hs-collapse="#card-body-id" class="text-gray-400 hover:text-gray-600 transition-colors"><i class="fas fa-minus"></i></button>` |
| `<div class="card-body">` | `<div id="card-body-id" class="hs-collapse open px-5 py-4 transition-all duration-300">` |
| `<div class="card-footer">` | `<div class="px-5 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl">` |

### 3.5 Modals

| Bootstrap 4 | Preline Overlay |
|---|---|
| `<div class="modal fade" id="modal" tabindex="-1">` | `<div id="modal" class="hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto [--overlay-backdrop:static]" role="dialog">` |
| `<div class="modal-dialog modal-lg">` | `<div class="hs-overlay-open:mt-7 hs-overlay-open:opacity-100 hs-overlay-open:duration-500 mt-0 opacity-0 ease-out transition-all sm:max-w-2xl sm:w-full m-3 sm:mx-auto">` |
| `<div class="modal-dialog modal-xl">` | `sm:max-w-4xl` instead of `sm:max-w-2xl` |
| `<div class="modal-dialog modal-md">` | `sm:max-w-lg` instead of `sm:max-w-2xl` |
| `<div class="modal-content">` | `<div class="flex flex-col bg-white border border-gray-200 shadow-lg rounded-xl">` |
| `<div class="modal-header">` | `<div class="flex items-center justify-between px-5 py-4 border-b border-gray-200">` |
| `<h5 class="modal-title" id="label">` | `<h3 class="text-lg font-semibold text-gray-800" id="label">` |
| `<button class="close" data-dismiss="modal">` | `<button type="button" class="text-gray-400 hover:text-gray-600 transition-colors" data-hs-overlay="#modal"><svg class="w-5 h-5" ...close icon.../></button>` |
| `<div class="modal-body">` | `<div class="px-5 py-4 overflow-y-auto max-h-[70vh]">` |
| `<div class="modal-footer">` | `<div class="flex items-center justify-end gap-2 px-5 py-3 border-t border-gray-200">` |

**Modal trigger buttons:**
| Bootstrap 4 | Preline |
|---|---|
| `data-toggle="modal" data-target="#modal"` | `data-hs-overlay="#modal"` |
| `data-dismiss="modal"` | `data-hs-overlay="#modal"` (on close button) |
| `$('#modal').modal('show')` | `HSOverlay.open(document.querySelector('#modal'))` |
| `$('#modal').modal('hide')` | `HSOverlay.close(document.querySelector('#modal'))` |
| `$('#modal-add').modal('hide')` | `HSOverlay.close(document.querySelector('#modal-add'))` |
| `shown.bs.modal` event | `open.hs.overlay` event |
| `hidden.bs.modal` event | `close.hs.overlay` event |

**JS Migration for modal show/hide:**

Every `$('#modal').modal('show')` in the codebase needs to change to:
```javascript
const modalEl = document.querySelector('#modal');
HSOverlay.open(modalEl);
```

Every `$('#modal').modal('hide')` changes to:
```javascript
const modalEl = document.querySelector('#modal');
HSOverlay.close(modalEl);
```

Every `$('#modal').on('shown.bs.modal', fn)` changes to:
```javascript
document.querySelector('#modal').addEventListener('open.hs.overlay', fn);
```

### 3.6 Info Boxes

| AdminLTE | Tailwind |
|---|---|
| `<div class="info-box">` | `<div class="flex items-center gap-4 rounded-xl bg-white p-4 shadow-sm border border-gray-100">` |
| `<span class="info-box-icon elevation-1" style="background-color: #0a1431ff;">` | `<div class="flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-lg bg-primary-dark">` |
| `<i class="fa-solid fa-user text-white">` | `<i class="fa-solid fa-user text-white text-lg"></i>` |
| `<div class="info-box-content">` | `<div>` |
| `<span class="info-box-text">` | `<p class="text-xs text-gray-500">` |
| `<span class="info-box-number">` / `<strong>` | `<p class="text-lg font-bold text-gray-800">` |

### 3.7 Tables

| Bootstrap 4 | Tailwind |
|---|---|
| `<div class="table-responsive">` | `<div class="overflow-x-auto">` |
| `<table class="table table-bordered table-striped" id="example1">` | `<table class="w-full text-sm" id="example1">` |
| `<thead>` | `<thead class="bg-gray-50">` |
| `<tr style="white-space: nowrap;">` | `<tr class="whitespace-nowrap">` |
| `<th style="width: 10%; text-align: center;">No</th>` | `<th class="w-[10%] text-center px-4 py-3 text-xs font-semibold uppercase tracking-wider text-gray-600">No</th>` |
| `<td style="text-align: center;">` | `<td class="text-center px-4 py-3">` |
| `<td style="white-space: nowrap;">` | `<td class="whitespace-nowrap px-4 py-3">` |

### 3.8 Buttons

| Bootstrap 4 | Tailwind |
|---|---|
| `btn btn-primary btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light transition-colors` |
| `btn btn-primary btn-block` | Add `w-full justify-center` to above |
| `btn btn-sm btn-primary-gradient` | `inline-flex items-center gap-1.5 rounded-lg btn-gradient-primary px-3 py-1.5 text-sm font-medium` |
| `btn btn-secondary btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-gray-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-gray-600 transition-colors` |
| `btn btn-success btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-green-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-green-700 transition-colors` |
| `btn btn-warning btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-amber-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-600 transition-colors` |
| `btn btn-danger btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-destructive px-3 py-1.5 text-sm font-medium text-white hover:bg-red-700 transition-colors` |
| `btn btn-info btn-sm` | `inline-flex items-center gap-1.5 rounded-lg bg-cyan-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-cyan-700 transition-colors` |
| `btn btn-tool` | `text-gray-400 hover:text-gray-600 transition-colors p-1` |

### 3.9 Forms

| Bootstrap 4 | Tailwind |
|---|---|
| `<div class="form-group">` | `<div class="mb-4">` |
| `<label for="x">Label</label>` | `<label for="x" class="block text-sm font-medium text-gray-700 mb-1">Label</label>` |
| `<input class="form-control">` | `<input class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">` |
| `<select class="form-control select2bs4">` | `<select class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm select2bs4">` |
| `<textarea class="form-control">` | `<textarea class="tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm text-gray-800 placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none transition-colors">` |
| `<div class="input-group">` | `<div class="flex">` |
| `<div class="input-group-prepend"><span class="input-group-text">+63</span></div>` | `<span class="input-group-text-tw inline-flex items-center rounded-l-lg border border-r-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">+63</span>` + add `rounded-l-none` to the adjacent input |
| `<div class="input-group-append"><div class="input-group-text">icon</div></div>` | `<span class="input-group-text-tw inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3 text-sm text-gray-500">icon</span>` + add `rounded-r-none` to the adjacent input |
| `<div class="custom-file"><input class="custom-file-input">` | `<input type="file" class="block w-full text-sm text-gray-500 file:mr-4 file:rounded-lg file:border-0 file:bg-primary file:px-4 file:py-2 file:text-sm file:font-medium file:text-white hover:file:bg-primary-light">` |
| `<div class="form-check form-check-inline">` | `<div class="inline-flex items-center gap-2">` |
| `<input class="form-check-input" type="radio">` | `<input type="radio" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">` |

### 3.10 Badges

| Bootstrap 4 | Tailwind |
|---|---|
| `badge badge-success` | `inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800` |
| `badge badge-warning` | `... bg-amber-100 ... text-amber-800` |
| `badge badge-danger` | `... bg-red-100 ... text-red-800` |
| `badge badge-info` | `... bg-cyan-100 ... text-cyan-800` |
| `badge badge-secondary` | `... bg-gray-100 ... text-gray-800` |

### 3.11 Breadcrumbs

| Bootstrap 4 | Tailwind |
|---|---|
| `<ol class="breadcrumb float-sm-right">` | `<nav class="flex"><ol class="flex items-center gap-1.5 text-sm">` |
| `<li class="breadcrumb-item"><a href="#">Dashboard</a></li>` | `<li><a href="#" class="text-primary hover:underline">Dashboard</a></li>` |
| Breadcrumb separator (auto by Bootstrap) | Add `<li class="text-gray-400">/</li>` between items |

### 3.12 Grid System

| Bootstrap 4 | Tailwind |
|---|---|
| `row` | `grid grid-cols-12 gap-4` or `flex flex-wrap -mx-2` |
| `col-12` | `col-span-12` |
| `col-sm-6` | `sm:col-span-6` |
| `col-md-3` | `md:col-span-3` |
| `col-md-4` | `md:col-span-4` |
| `col-md-6` | `md:col-span-6` |
| `col-md-12` | `md:col-span-12` (or just `col-span-12`) |
| `col-lg-4` | `lg:col-span-4` |

**Simpler alternative** (used when only 2-4 equal columns):
| Bootstrap 4 | Tailwind |
|---|---|
| `<div class="row"><div class="col-md-6">` x2 | `<div class="grid grid-cols-1 md:grid-cols-2 gap-4">` |
| `<div class="row"><div class="col-md-4">` x3 | `<div class="grid grid-cols-1 md:grid-cols-3 gap-4">` |
| `<div class="row"><div class="col-12 col-sm-6 col-md-3">` x4 | `<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">` |

### 3.13 Utility Classes

| Bootstrap 4 | Tailwind |
|---|---|
| `d-flex` | `flex` |
| `d-none` | `hidden` |
| `d-block` | `block` |
| `d-none d-sm-inline-block` | `hidden sm:inline-block` |
| `justify-content-between` | `justify-between` |
| `justify-content-start` | `justify-start` |
| `justify-content-center` | `justify-center` |
| `align-items-center` | `items-center` |
| `text-center` | `text-center` |
| `text-left` | `text-left` |
| `text-right` | `text-right` |
| `float-sm-right` | `sm:float-right` (or use flex) |
| `mb-2` | `mb-2` |
| `mb-3` | `mb-3` |
| `mb-4` | `mb-4` |
| `mt-2` | `mt-2` |
| `mt-3` | `mt-3` |
| `p-3` | `p-3` |
| `pl-3` | `pl-3` |
| `pt-3` | `pt-3` |
| `pb-3` | `pb-3` |
| `w-100` | `w-full` |
| `h-100` | `h-full` |
| `text-white` | `text-white` |
| `text-danger` | `text-red-600` |
| `text-success` | `text-green-600` |
| `text-warning` | `text-amber-600` |
| `text-info` | `text-cyan-600` |
| `text-muted` | `text-gray-500` |
| `font-weight-bold` / `style="font-weight: 800"` | `font-bold` / `font-extrabold` |
| `elevation-1` | `shadow-sm` |
| `elevation-2` | `shadow` |
| `img-circle` | `rounded-full` |
| `img-fluid` | `w-full h-auto` |
| `img-thumbnail` | `rounded border border-gray-200 p-1` |
| `rounded-circle` | `rounded-full` |
| `sr-only` | `sr-only` |

### 3.14 Alert Dismiss

| Bootstrap 4 | Preline |
|---|---|
| `<div class="alert alert-success alert-dismissible">` | `<div class="rounded-lg bg-green-50 border border-green-200 p-4 text-sm text-green-800 alert-auto-dismiss" role="alert">` |
| Close button: `<button class="close" data-dismiss="alert">` | `<button type="button" data-hs-remove-element=".alert-auto-dismiss" class="...">` |

### 3.15 Tooltips

| Bootstrap 4 | Preline |
|---|---|
| `data-toggle="tooltip" title="..."` | Wrap element with Preline tooltip: `<div class="hs-tooltip inline-block"><button class="hs-tooltip-toggle">...<span class="hs-tooltip-content ...">tooltip text</span></button></div>` |

### 3.16 Dropdowns

| Bootstrap 4 | Preline |
|---|---|
| `<div class="dropdown">` | `<div class="hs-dropdown relative inline-flex">` |
| `<button data-toggle="dropdown">` | `<button id="hs-dropdown-btn" type="button" class="hs-dropdown-toggle ...">` |
| `<div class="dropdown-menu">` | `<div class="hs-dropdown-menu hidden ...">` |

---

## 4. File-by-File Conversion Order

### Phase A: Layout Shell (do these first, in order)

#### A1. `main/header.php` ✅ DONE

**Current state**: AdminLTE CSS, Google Fonts, Leaflet/OL CSS, DataTables CSS, Select2 CSS, Summernote CSS, Font Awesome, custom `<style>` block, jQuery + SweetAlert2 in head.

**Changes**:
1. **REMOVE** `adminlte.min.css` link
2. **REMOVE** `tempusdominus-bootstrap-4.min.css` link
3. **REMOVE** `Source Sans Pro` Google Font link
4. **ADD** Tailwind CDN play script + config (Section 1.1, 1.3)
5. **ADD** Inter font link
6. **ADD** Preline CSS (none needed, Tailwind handles it)
7. **REPLACE** the entire `<style>` block with the new one from Section 1.4
8. **CHANGE** `<body class="hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">` to `<body class="bg-background font-sans text-gray-800">`
9. **CHANGE** `<div class="wrapper">` to `<div class="min-h-screen">`

**KEEP unchanged**: All Leaflet CSS, OpenLayers CSS, DataTables CSS, Select2 CSS, Summernote CSS, Font Awesome, Ionicons, map.min.css, jQuery CDN, SweetAlert2 CDN, favicon logic, OpenLayers JS.

#### A2. `main/footer.php` ✅ DONE

**Current state**: jQuery (fixed version), jQuery UI, Bootstrap JS, AdminLTE JS, Select2, Summernote, DataTables suite, JSZip/PDFMake, Moment.js, inline scripts (DataTable inits, Select2 inits, flash messages, etc.).

**Changes**:
1. **REMOVE** `bootstrap.bundle.min.js` script tag
2. **REMOVE** `adminlte.js` script tag
3. **ADD** Preline JS at the bottom (Section 1.2)
4. **CHANGE** `<footer class="main-footer text-sm no-print">` to `<footer class="lg:ml-64 border-t border-gray-200 bg-white px-6 py-3 text-sm text-gray-500 no-print">`
5. **REMOVE** `<aside class="control-sidebar control-sidebar-dark">` block entirely
6. **REMOVE** closing `</div><!-- ./wrapper -->` (now handled differently)
7. Keep the `<div class="float-right d-none d-sm-inline-block">` but convert to `<div class="hidden sm:inline-block float-right">`

**KEEP unchanged**: jQuery 3.6.0, jQuery UI, Select2, Summernote, all DataTables scripts, JSZip, PDFMake, Moment.js, all inline `<script>` blocks (DataTable inits, flash messages, SweetAlert handlers, etc.).

**IMPORTANT JS changes in footer.php inline scripts**:
- The treeview toggle JS (`$(document).on('click', '.has-treeview > a', ...)`) can be **REMOVED** - Preline accordion handles this automatically.
- All `$('.select2bs4').select2({theme: 'bootstrap4'})` calls: **KEEP** as-is. Select2 with bootstrap4 theme still works fine since we keep the Select2 CSS.
- All `$('.select2For_modal').each(...)` calls: **KEEP** but change `$(this).closest('.modal')` to `$(this).closest('[role="dialog"]')` for Preline overlay compatibility.

#### A3. `main/topbar.php` ✅ DONE

**Current state**: AdminLTE navbar with pushmenu toggle button and sign-out link.

**Replace entire file with:**

```php
<!-- Topbar -->
<header class="fixed top-0 right-0 left-0 z-50 flex items-center justify-between bg-primary px-4 h-16 lg:ml-64 transition-all duration-300">
    <!-- Left: Hamburger (mobile only) -->
    <div class="flex items-center">
        <button type="button"
                class="lg:hidden inline-flex items-center justify-center w-10 h-10 rounded-lg text-white hover:bg-white/10 transition-colors"
                data-hs-overlay="#sidebar"
                aria-label="Toggle sidebar">
            <i class="fa fa-bars text-lg"></i>
        </button>
    </div>

    <!-- Right: Sign Out -->
    <div>
        <a href="<?php echo base_url('auth/logout'); ?>"
           class="swt flex items-center gap-2 rounded-lg px-3 py-2 text-sm font-bold text-white hover:bg-white/10 transition-colors">
            <i class="fa-solid fa-arrow-right-from-bracket"></i>
            <span class="whitespace-nowrap">Sign Out</span>
        </a>
    </div>
</header>
```

#### A4. `main/sidebar.php` ✅ DONE

**Current state**: AdminLTE sidebar with brand logo, user panel, nav-pills sidebar menu, treeview items for Job List and Report.

**Replace entire file** with the Preline Offcanvas sidebar from Section 3.3. Key points:
- Use `hs-overlay` with `[--auto-close:lg]` for responsive behavior
- Job List treeview becomes Preline `hs-accordion`
- Report treeview becomes second Preline `hs-accordion`
- All active link highlighting PHP logic stays the same
- `count_notif_sidebar`, `count_line`, `count_short`, etc. class targets stay the same for AJAX updates
- `count_reschedule` bell-shake span stays the same
- Role-based `<?php if($this->session->userdata('Role') != 1) : ?>` logic stays the same
- Subscription-based `<?php if($this->session->userdata('CompanySubscribe') == 2) : ?>` logic stays the same

#### A5. `main/index.php` ✅ DONE

**Current state**: Simple wrapper echoing $header, $topbar, $sidebar, $content, $ourjs, $footer. Has exportExcel conditional.

**Replace with:**

```php
<div>
    <?php echo $header; ?>
    <?php
        if(!isset($_POST['exportExcel'])) {
            echo $topbar;
        }
    ?>
    <?php
        if(!isset($_POST['exportExcel'])) {
            echo $sidebar;
        }
    ?>
    <main class="<?= !isset($_POST['exportExcel']) ? 'lg:ml-64 pt-16 pb-16' : '' ?> min-h-screen transition-all duration-300" id="content-blur">
        <?php echo $content; ?>
    </main>
    <?php echo $ourjs; ?>
    <?php echo $footer; ?>
</div>
```

> Note: `id="content-blur"` is added because ourjs.php's `sweatAlertLoader()` function uses `$('#content-blur').addClass('blur')`.

#### A6. `main/ourjs.php` ✅ DONE

> **Deviation notes (Phase A):**
> - A1: Added Option B flash message compat classes (`.alert-success`, `.alert-danger`, etc.) and DataTables Bootstrap layout compat (`.row`, `.col-sm-12`) to the `<style>` block beyond what Section 1.4 specified, to ensure DataTables renders properly without Bootstrap CSS.
> - A2: Added Select2 re-initialization listener for Preline overlays (`open.hs.overlay` event) per Section 7.2.
> - A6: Added global `showModal()`/`hideModal()` helper functions per Section 7.5. Fixed duplicate `contentType` property in `createImage()`.

---

### Phase B: Auth Pages — UP NEXT

**Current state**: JS utility functions (sweatAlertLoader, refreshTable, CRUD helpers, countJobInSidebar, playNotificationSound, Select2 inits, DataTable inits, flash message handling, sign-out confirmation).

**Changes**:
1. In `create()`, `update()`, `updateImage()`, `createImage()`: Change `$('#modal-add').modal('hide')` to `HSOverlay.close(document.querySelector('#modal-add'))` (or `#modal` depending on what's actually targeted - check each function).
2. In `refreshTable()`: No changes needed (DataTables is preserved).
3. In `countJobInSidebar()`: No changes needed (class selectors are preserved in new sidebar).
4. `$('.select2For_modal').each(...)`: Change `$(this).closest('.modal')` to `$(this).closest('[role="dialog"]')`.
5. The treeview icon toggle JS block can be **REMOVED** (Preline handles it).
6. **REMOVE** the `$.widget.bridge('uibutton', $.ui.button)` duplicate call at line 60 (already in footer).

**KEEP everything else**: All DataTable initializations, Select2 inits, flash message handling, SweetAlert handlers, file input handlers, previewImage, playNotificationSound, etc.

---

### Phase B: Auth Pages

#### B1. `auth/header.php` ✅ DONE

**Current state**: Minimal head with AdminLTE CSS, Font Awesome, icheck-bootstrap.

**Replace with:**

```php
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>EFMS | Administrator</title>

    <link rel="icon" type="image/jpeg" href="<?= base_url('assets/dist/logo_efms.jpg') ?>?v=2" />
    <link rel="shortcut icon" type="image/jpeg" href="<?= base_url('assets/dist/logo_efms.jpg') ?>?v=2" />

    <!-- Tailwind CSS v3 CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
    tailwind.config = {
        theme: {
            extend: {
                fontFamily: { sans: ['Inter', 'sans-serif'] },
                colors: {
                    primary: { DEFAULT: '#070f26', dark: '#0a1431' },
                    accent: { DEFAULT: '#4e54c8', light: '#8f94fb' },
                    destructive: '#da1e26',
                },
            },
        },
    }
    </script>

    <!-- Inter Font -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
</head>
<body class="bg-gray-100 font-sans min-h-screen flex items-center justify-center">
```

#### B2. `auth/footer.php` ✅ DONE

**Current state**: jQuery, Bootstrap JS, AdminLTE JS.

**Replace with:**

```php
<!-- jQuery (needed only if login page uses jQuery) -->
<script src="<?php echo base_url('assets/plugins/jquery/jquery.min.js'); ?>"></script>

<!-- Preline UI -->
<script src="https://cdn.jsdelivr.net/npm/preline@2/dist/preline.min.js"></script>
</body>
</html>
```

> Note: The login page uses vanilla JS for validation, so jQuery may not be strictly needed here. Keep it for safety since `page_login.php` doesn't use jQuery-dependent code.

#### B3. `auth/index.php` ✅ DONE (no changes needed)

No structural change needed. It just echoes header, content, footer.

#### B4. `auth/login/page_login.php` ✅ DONE

> **Deviation notes (Phase B):**
> - B1: Added `.alert-success` and `.alert-danger` compat classes in the auth header `<style>` for flash messages on the login page.
> - B4: Validation JS `group.parentElement` approach preserved as-is — it naturally targets the wrapper `div` (`#email-wrapper` / `#password-wrapper`) in the new HTML structure.

**Current state**: AdminLTE `login-box` layout with Bootstrap card, form-control inputs, input-group-append icons, custom CSS for gradient button and field errors.

**Replace the `<style>` and HTML with:**

```php
<style>
.field-error input { @apply !border-red-500; }
.field-error .input-icon-box { @apply !border-red-500; }
.inline-error { @apply text-red-500 text-xs mt-1 block; }
</style>

<div class="w-full max-w-md mx-auto px-4">
    <!-- Card -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
        <!-- Logo Header -->
        <div class="bg-white px-6 pt-8 pb-4 text-center">
            <img class="h-14 mx-auto object-contain" src="<?= base_url('assets/dist/logo_efms.jpg') ?>" alt="EFMS Logo">
        </div>

        <!-- Form Body -->
        <div class="px-8 pb-8">
            <p class="text-center text-gray-500 text-sm mb-6">Administrator Sign In</p>

            <?php echo $this->session->flashdata('message'); ?>

            <form id="loginForm" action="<?php echo base_url('auth/login'); ?>" method="post" novalidate>
                <!-- Email -->
                <div class="mb-4" id="email-wrapper">
                    <div class="flex" id="email-group">
                        <input type="email" autofocus
                               class="flex-1 rounded-l-lg border border-gray-300 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                               name="email" id="login-email" placeholder="Email" autocomplete="off">
                        <span class="input-icon-box inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3">
                            <i class="fa fa-envelope text-gray-400 text-sm"></i>
                        </span>
                    </div>
                    <span class="inline-error" id="email-error"></span>
                </div>

                <!-- Password -->
                <div class="mb-6" id="password-wrapper">
                    <div class="flex" id="password-group">
                        <input type="password"
                               class="flex-1 rounded-l-lg border border-gray-300 px-3 py-2.5 text-sm placeholder-gray-400 focus:border-primary focus:ring-2 focus:ring-primary/20 focus:outline-none"
                               name="password" id="login-password" placeholder="Password">
                        <span class="input-icon-box inline-flex items-center rounded-r-lg border border-l-0 border-gray-300 bg-gray-50 px-3">
                            <i class="fa fa-lock text-gray-400 text-sm"></i>
                        </span>
                    </div>
                    <span class="inline-error" id="password-error"></span>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full rounded-lg bg-gradient-to-r from-[#251abe] to-[#9f22f0] px-4 py-2.5 text-sm font-semibold text-white hover:scale-[1.02] transition-transform">
                    Sign In
                </button>
            </form>
        </div>
    </div>
</div>
```

**JS validation**: Keep the existing `<script>` block at the bottom. Change `group.parentElement.classList.add('field-error')` to target the wrapper div (adjust selectors to use `#email-wrapper` / `#password-wrapper` instead of `group.parentElement`).

---

### Phase C: Dashboard & Simple Pages

#### C1. `main/home/page_home.php` (Dashboard) ✅ DONE

**Changes**:
1. `<div class="content-wrapper">` -> Remove (now handled by `index.php`'s `<main>`)
2. `<section class="content-header">` -> `<div class="px-4 sm:px-6 lg:px-8 py-4">`
3. Breadcrumb: Convert per Section 3.11
4. `<section class="content"><div class="container-fluid">` -> `<div class="px-4 sm:px-6 lg:px-8 pb-6">`
5. Info boxes row `<div class="row">` -> `<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">`
6. Each `<div class="col-12 col-sm-6 col-md-4">` -> remove, grid handles sizing
7. Each info-box: Convert per Section 3.6
8. Cards (Driver On Duty, Driver Off Duty, Ongoing Job, Finished Job):
   - `<div class="row">` around card pairs -> `<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">`
   - Each card: Convert per Section 3.4
   - `data-card-widget="collapse"` button -> Preline `data-hs-collapse="#card-body-xx"` per Section 3.4
   - Tables inside cards: Convert per Section 3.7

**KEEP**: Chart.js script tag, jQuery slim script tag (though it should probably be removed since jQuery is already loaded), `table_dashboard1` class (add Tailwind override: `.table_dashboard1 { @apply text-[13px]; }`)

#### C2. `main/home/page_choose.php` (Choose Outlet) ✅ DONE

This is a standalone page (has its own `<head>`). It's rarely used in FMS.

**Changes**:
1. Replace the `<head>` with Tailwind CDN setup (similar to auth/header.php)
2. `<body class="hold-transition lockscreen">` -> `<body class="bg-gray-100 font-sans min-h-screen">`
3. `<div class="container">` -> `<div class="max-w-5xl mx-auto px-4 py-8">`
4. `<div class="login-logo">` -> `<h1 class="text-2xl font-bold text-center mb-8">`
5. `<div class="row">` -> `<div class="grid grid-cols-1 md:grid-cols-3 gap-6">`
6. Cards: Convert per Section 3.4
7. Buttons: Convert per Section 3.8

#### C3. `main/user/page_user.php` (Rider Management) ✅ DONE

**Changes**:
1. Remove `<div class="content-wrapper">` wrapper
2. Convert content-header and breadcrumb
3. Flash message area: Convert `alert-success` to Tailwind alert
4. Card header with company filter form:
   - `<div class="form-row align-items-end">` -> `<div class="flex flex-wrap items-end gap-3">`
   - `<div class="col-auto">` -> remove, flex handles sizing
   - Select with `select2bs4`: Keep select2 class, convert outer styling
5. Button row (Import Excel, Download Example, Add User): Convert buttons per Section 3.8
6. DataTable (`#example3`): Convert table per Section 3.7
7. **Modals** (Add/Edit User, Delete User):
   - Convert both modals from Bootstrap to Preline per Section 3.5
   - Keep all form fields, convert `form-group` to `mb-4`, `form-control` to `tw-input`
   - Keep `select2For_modal` class
   - Phone input-group: Convert per Section 3.9
   - Badge (`badge-secondary`, `badge-warning`, `badge-success`): Convert per Section 3.10

**JS changes**:
- `modal.modal('show')` -> `HSOverlay.open(document.querySelector('#modal'))`
- `modal.modal('hide')` -> `HSOverlay.close(document.querySelector('#modal'))`
- `$('#modal').on('shown.bs.modal', ...)` -> `document.querySelector('#modal').addEventListener('open.hs.overlay', ...)`
- Import Excel AJAX handlers: Keep as-is
- Form validation JS: Keep as-is (just ensure `.form-group` references are changed to `.mb-4` or use parent selectors)
- **Note**: Validation helpers `setFieldError`/`clearFieldError` use `$field.closest('.form-group')`. Change to `$field.closest('.mb-4')` or add a `.form-field` class to form field wrappers.

#### C4. `main/company/page_company.php` (Company Management) ✅ DONE

**Changes** (same pattern as user page):
1. Remove content-wrapper
2. Convert content-header, breadcrumb
3. Card with buttons: Convert `btn-primary-gradient` to `btn-gradient-primary` class
4. Table: Convert per Section 3.7
5. **Three modals**: Add/Edit Company, Delete Company, Update Profile
   - All three: Convert per Section 3.5
   - Company logo preview/upload: Convert custom-file to Tailwind file input per Section 3.9
   - Radio buttons (package Basic/Pro): Convert per Section 3.9
   - Password toggle button (eye/eye-slash): Keep JS, convert button to Tailwind styling
6. Data Synchronization SweetAlert flash: Keep as-is

**JS changes**: Same modal pattern as user page. All `modal('show')`/`modal('hide')` -> Preline equivalents.

#### C5. `main/customer/page_customer.php` (Customer Management) ✅ DONE

**Changes** (same pattern):
1. Remove content-wrapper
2. Convert content-header, breadcrumb
3. Card header with Import Excel and Add Customer buttons
4. Table with Google Maps links: Keep `btn btn-sm btn-info` on map links, convert to Tailwind
5. **Two modals**: Add/Edit Customer (with Leaflet map), Delete Customer
6. Leaflet map in modal: **Critical** - See Edge Cases Section 7.2

**JS changes**:
- Modal show/hide -> Preline
- `$('#modal').on('shown.bs.modal', function() { map.invalidateSize(); })` -> `document.querySelector('#modal').addEventListener('open.hs.overlay', function() { setTimeout(() => { map.invalidateSize(); }, 300); })`
- Keep all Leaflet/Geocoder initialization

> **Deviation notes (Phase C):**
> - C1: Kept `table_dashboard1` inline style override instead of `@apply` (Play CDN renders inline). Card collapse buttons use `data-hs-collapse` attribute.
> - C2: Already converted in prior session (standalone page with own `<head>`). No further deviations.
> - C3: `.form-group` class preserved alongside `mb-4` as JS validation hook for `$field.closest('.form-group')`. Badge classes converted from Bootstrap to Tailwind with dynamic `removeClass`/`addClass`. SweetAlert `customClass.confirmButton` updated to Tailwind utilities.
> - C4: Fixed extra `>` in `formDelete` action attribute. Leaflet `shown.bs.modal` → `open.hs.overlay` with 400ms delay. Password toggle and file upload inputs converted to Tailwind.
> - C5: Leaflet map `shown.bs.modal` → `open.hs.overlay` event listener with 400ms delay. `input-group` for phone number → Tailwind flex with rounded segments. Import Excel SweetAlert `customClass.confirmButton` updated to Tailwind utilities.

---

### Phase D: Complex Pages — UP NEXT

#### D1. `main/job/page_job.php` (Job List - MOST COMPLEX) ✅ DONE

**Changes**:
1. Remove content-wrapper
2. Convert content-header with `<h1>Job <?= $label_job ?></h1>`
3. Card header with 4 info boxes (Today's Job, Ongoing, Upcoming, Reschedule):
   - `<div class="row">` -> `<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4">`
   - Each info-box: Convert per Section 3.6, including colored backgrounds (#ffc107, #28a745, #17a2b8)
4. Add Job button: Convert to Tailwind
5. DataTable (`#tableJobRider`): Server-side with `columns` array - table structure converts per Section 3.7
6. **Five modals**:
   - `#modal` (Add/Edit Job, `modal-lg`): Convert per Section 3.5, keep form fields
   - `#modal_delete` (Delete Job, `modal-md`): Convert
   - `#modal_detail` (Job Detail, `modal-lg`): Convert, keep `.content_header` custom styles (already Tailwind-compatible)
   - `#modal_camera` (Photos, `modal-xl`): Convert, keep `.job-gallery` styles
   - `#modal_history_cancel_job` (Cancel History, `modal-lg`): Convert, keep `.load()` pattern

7. **Custom `<style>` block**: Keep these in the page (they're page-specific):
   - `.content_header`, `.row-item`, `.label`, `.value` - used for job detail display
   - `.job-gallery img` styles - used for photo gallery
   - `.container_status_job` - used for status badge in detail modal
   - `.ongoing_job` / `.completed_job` page-level overrides (different from header global ones)

**JS changes (extensive)**:
- All 5 modal instances: `$('#xxx').modal('show')` -> `HSOverlay.open(document.querySelector('#xxx'))`
- `$('#modal').on('shown.bs.modal', function() { map.invalidateSize(); })` -> Preline event
- DataTable with `setInterval` for auto-reload: Keep as-is
- `refreshCard()` AJAX: Keep as-is
- All button click handlers: Keep event delegation patterns, only change modal API calls
- Button renders in DataTable columns (JS template literals): Keep `.btn` class names as these are converted to Tailwind in the global styles. **OR** update template literals to use Tailwind classes directly.

> **Recommendation**: For DataTable column renders that generate HTML buttons via JS, create utility CSS classes that match Tailwind:
> ```css
> .btn-tw-warning { @apply inline-flex items-center justify-center rounded-lg bg-amber-500 px-2 py-1 text-xs text-white hover:bg-amber-600 transition-colors; }
> .btn-tw-danger { @apply inline-flex items-center justify-center rounded-lg bg-destructive px-2 py-1 text-xs text-white hover:bg-red-700 transition-colors; }
> .btn-tw-success { @apply inline-flex items-center justify-center rounded-lg bg-green-600 px-2 py-1 text-xs text-white hover:bg-green-700 transition-colors; }
> .btn-tw-info { @apply inline-flex items-center justify-center rounded-lg bg-cyan-600 px-2 py-1 text-xs text-white hover:bg-cyan-700 transition-colors; }
> ```
> Then use these in the DataTable `render` functions instead of Bootstrap `.btn` classes.

#### D2. `main/job/page_reschedule_job.php` ✅ DONE

Same pattern as job page. Has:
- Content-wrapper, content-header
- Card with DataTable
- Modals for approving/rejecting reschedule requests
- Status badges

**Changes**: Same conversion pattern. Convert layout, cards, table, modals, buttons.

#### D3. `main/job/page_history_cancel_job.php` ✅ DONE

Loaded via `.load()` into a modal in page_job.php. Typically a partial view (no layout wrapper).

**Changes**: Convert any Bootstrap classes in the partial to Tailwind equivalents. This is usually a simple table or list.

#### D4. `main/job/page_history_reschedule_job.php` ✅ DONE

Same as D3 - a partial view loaded into a modal.

#### D5. `main/job/page_job_summary.php` ✅ DONE

Similar to home page dashboard. Has:
- Content-header, content section
- Cards with tables/charts
- Export functionality

**Changes**: Standard conversion pattern.

> **Deviation notes (Phase D):**
> - D1: All 5 modals converted. DataTable action buttons in JS renders changed from `btn btn-sm btn-*` to `.btn-tw-*` classes. Photo gallery changed from Bootstrap `row`/`col-12 col-md-3` grid to Tailwind `grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-4`. Removed standalone jQuery CDN (already loaded globally). `d-none` → `hidden` in detail modal JS.
> - D2: One modal (reject). Filter form converted from Bootstrap `row`/`col-md-3` to Tailwind grid. DataTable action buttons → `.btn-tw-success`/`.btn-tw-danger`. Removed standalone jQuery CDN.
> - D3/D4: Simple partial tables converted from `table table-bordered table-striped` to Tailwind table with `bg-gray-50` thead, `border-b border-gray-100 hover:bg-gray-50` rows.
> - D5: Shared modal for cancel/reschedule history. Added `.btn-tw-primary` utility class to `header.php` (not previously defined). `text-danger` → `text-red-600` in DataTable renders. Removed standalone jQuery CDN. Customer filter select keeps `select2bs4` class.

---

### Phase E: Maps & Vehicle — UP NEXT

#### E1. `main/map/map.php` (OpenLayers Map) ✅ DONE

**Current state**: Uses `content-wrapper` layout, OpenLayers map div with custom CSS classes (`.map-loader`, `.spinner`, `.ol-legend`, `.custom-zoom`, `.search-box`, `.hover-tooltip`).

**Changes**:
1. Remove `content-wrapper` wrapper
2. Convert `content-header`
3. The map div and its children: **KEEP ALL CUSTOM CSS CLASSES** - these are defined in `map.min.css` and are specific to OpenLayers. Do not convert these.
4. The `<script>` referencing `window.objectsMergeUrl`: Keep as-is
5. OpenLayers initialization in `assets/map/map.js`: **DO NOT TOUCH** - this is a separate JS file

**Minimal changes** - this page mostly needs layout wrapper conversion only.

#### E2. `main/vehicle/vehicle.php` ✅ DONE

**Changes**:
1. Remove content-wrapper
2. Convert content-header
3. Card: `card card-info card-outline` -> Tailwind card with cyan/info border: `bg-white rounded-xl shadow-sm border border-cyan-200`
4. Loading spinner: `<div class="spinner-border text-info">` -> `<div class="animate-spin rounded-full h-12 w-12 border-4 border-cyan-200 border-t-cyan-600"></div>`
5. `<span class="sr-only">Loading...</span>` -> `<span class="sr-only">Loading...</span>` (same in Tailwind)
6. DataTable (`#vehicleTable`): Convert table per Section 3.7
7. Error state HTML in JS: Update icon/text classes to Tailwind

**JS**: Keep all DataTable initialization. The server-side AJAX pattern is unchanged.

---

### Phase F: Reports

All report pages follow a very similar pattern: content-wrapper, content-header, card with DataTable (often with export buttons), sometimes filter forms.

#### F1. `main/report/reportDriver.php` ✅ DONE

**Changes**:
1. Remove content-wrapper
2. Convert content-header
3. The custom `<style>` block: Keep page-specific DataTable column alignment styles but convert to Tailwind where possible
4. DataTable with export buttons (Excel, PDF, Print, ColVis): Keep all DataTable button initialization. The `.dt-button` classes are handled by our global DataTables override in Section 1.4
5. Filter form (date range, company select): Convert form elements per Section 3.9
6. Cards: Convert per Section 3.4

#### F2. `main/report/reportJob.php` ✅ DONE

Same pattern as reportDriver. Convert layout, cards, tables, filter forms.

#### F3. `main/report/reportCustomer.php` ✅ DONE

Same pattern. Convert layout, cards, tables.

#### F4. `main/report/detail_driver.php` ✅ DONE

Detail view page. Has content-header, cards with info display and table.

**Changes**: Standard conversion. May have info-box or custom detail layouts.

#### F5. `main/report/detail_driver_cancel.php` ✅ DONE

Similar to detail_driver.php. Convert layout and display elements.

#### F6. `main/report/detail_job.php` ✅ DONE

Similar detail page. Standard conversion.

**Phase F Deviation Notes**:
- **F1 (reportDriver.php)**: 3 modals converted to Preline `hs-overlay`. `.modal('show')` → `showModal()`. Page-specific DataTable styles preserved. Filter form → Tailwind grid.
- **F2 (reportJob.php)**: Active section (Job Report per Customer) fully converted — layout, card, filter form, modal. Commented-out sections (Job Compliance Report, Job Evidence Report, and their photo modals) left as-is. Orphaned JS for those commented-out sections kept since it targets non-existent DOM elements (harmless dead code).
- **F3 (reportCustomer.php)**: Customer Retention Report converted — layout, card, filter form, 1 modal (`#customerDetailModal` → Preline hs-overlay). Commented-out Customer Engagement Report HTML left as-is (JS still present but targets non-existent DOM).
- **F4 (detail_driver.php)**: Partial loaded into modal. `table table-striped` → Tailwind table classes. `d-none` → `hidden`. `style="white-space: nowrap;"` → `whitespace-nowrap` class.
- **F5 (detail_driver_cancel.php)**: Same pattern as F4. Simple table → Tailwind table classes.
- **F6 (detail_job.php)**: Info tables → Tailwind borderless tables. `text-danger` → `text-red-600`. `row`/`col-md-6` → `grid grid-cols-1 md:grid-cols-2 gap-4`. Inline `style="font-weight: bold;"` → `font-bold` class. `<hr>` → `<hr class="border-gray-200">`.

---

### Phase G: Standalone Pages

#### G1. `main/forgot_password.php` ✅ DONE

**Current state**: Fully standalone page with its own inline CSS (no AdminLTE dependency). Has a custom-styled email template layout.

**Changes**:
1. This page uses **zero Bootstrap/AdminLTE classes** - it's all custom CSS
2. Optionally convert to Tailwind for consistency, but it's **low priority** since it works independently
3. If converting: Replace inline `<style>` with Tailwind utilities on each element
4. Key conversions:
   - `.email-container` -> `max-w-xl mx-auto my-5 bg-white rounded-lg shadow-lg overflow-hidden`
   - `.email-header` -> `bg-primary text-white text-center p-5`
   - `.reset-button` -> `inline-block my-5 px-6 py-3 bg-primary text-white rounded-md hover:bg-accent transition-colors`
   - `.password-form` -> `max-w-sm mx-auto p-5 bg-white border border-gray-200 rounded-lg shadow-sm`
5. Keep the password match validation JS as-is

#### G2. `main/success_forgot_password.php` ✅ DONE

Standalone success message page. Minimal conversion needed.

#### G3. `main/expired_forgot_password.php` ✅ DONE

Standalone expiry message page. Minimal conversion needed.

**Phase G Deviation Notes**:
- All three standalone pages had `<style>` blocks replaced with Tailwind utility classes inline.
- Tailwind CDN `<script>` added to each standalone page's `<head>` with the same primary/accent color config.
- Inter font loaded via Google Fonts for consistency.
- Password match validation JS preserved as-is in G1.
- `disabled` button state handled with `disabled:opacity-50 disabled:cursor-not-allowed` Tailwind utilities.
- Emoji icons (checkmark, warning) preserved using HTML entities.

---

## 5. JS Dependencies Preservation Guide

### 5.1 Script Tags to KEEP (in footer.php)

```html
<!-- jQuery 3.6.0 (FIXED - required by DataTables, Select2, Summernote, all custom JS) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- jQuery UI (needed for widget.bridge) -->
<script src="<?= base_url('assets/plugins/jquery-ui/jquery-ui.min.js') ?>"></script>

<!-- Select2 (KEEP - Preline's hs-select is too basic for AJAX/tagging) -->
<script src="<?= base_url('assets/plugins/select2/js/select2.full.min.js') ?>"></script>

<!-- Summernote (KEEP - WYSIWYG editor, no Preline equivalent) -->
<script src="<?= base_url('assets/plugins/summernote/summernote-bs4.min.js') ?>"></script>

<!-- DataTables Full Suite (KEEP - server-side processing, export, responsive) -->
<script src="<?= base_url('assets/plugins/datatables/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-responsive/js/dataTables.responsive.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-responsive/js/responsive.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.bootstrap4.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('assets/plugins/datatables-buttons/js/buttons.colVis.min.js') ?>"></script>

<!-- Export libraries -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>

<!-- Moment.js (KEEP - date formatting in DataTables renders) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
```

### 5.2 Script Tags in header.php to KEEP

```html
<!-- jQuery (CDN version in head for early use - will be overridden by fixed version in footer) -->
<script src="https://cdn.jsdelivr.net/npm/jquery"></script>

<!-- SweetAlert2 (KEEP - confirm dialogs, toasts, loading states) -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<!-- OpenLayers (KEEP - used for map.php vehicle tracking) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/ol.min.css" ...>
<script src="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/dist/ol.min.js" ...></script>
```

### 5.3 Script Tags to REMOVE

```html
<!-- REMOVE: Bootstrap 4 JS (replaced by Preline for modals/collapse/dropdowns) -->
<!-- <script src="assets/plugins/bootstrap/js/bootstrap.bundle.min.js"></script> -->

<!-- REMOVE: AdminLTE JS (replaced by Preline for sidebar/pushmenu/treeview/card-widget) -->
<!-- <script src="assets/dist/js/adminlte.js"></script> -->
```

### 5.4 CSS Files to KEEP

```html
<!-- DataTables Bootstrap4 CSS (KEEP - styles DataTables output, our Tailwind overrides layer on top) -->
<link rel="stylesheet" href="assets/plugins/datatables-bs4/css/dataTables.bootstrap4.min.css">
<link rel="stylesheet" href="assets/plugins/datatables-responsive/css/responsive.bootstrap4.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

<!-- Select2 CSS (KEEP - styles Select2 dropdowns) -->
<link rel="stylesheet" href="assets/plugins/select2/css/select2.min.css">
<link rel="stylesheet" href="assets/plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css">

<!-- Summernote CSS (KEEP - styles WYSIWYG editor) -->
<link rel="stylesheet" href="assets/plugins/summernote/summernote-bs4.min.css">

<!-- Leaflet CSS (KEEP - map styling) -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder/dist/Control.Geocoder.css">

<!-- OpenLayers CSS (KEEP) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/openlayers/10.6.1/ol.min.css" ...>

<!-- Font Awesome 7 (KEEP - all icon links) -->
<!-- Ionicons (KEEP) -->

<!-- Map custom CSS (KEEP) -->
<link rel="stylesheet" href="assets/css/map.min.css">
```

### 5.5 CSS Files to REMOVE

```html
<!-- REMOVE: AdminLTE core CSS (replaced by Tailwind) -->
<!-- <link rel="stylesheet" href="assets/dist/css/adminlte.min.css"> -->

<!-- REMOVE: Tempusdominus Bootstrap 4 (not actively used) -->
<!-- <link rel="stylesheet" href="assets/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css"> -->

<!-- REMOVE: Google Font Source Sans Pro (replaced by Inter) -->
<!-- <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback"> -->
```

### 5.6 DataTables Styling Overrides for Tailwind

The DataTables BS4 CSS creates Bootstrap-style table output. Our Tailwind overrides in Section 1.4 layer on top. If styling conflicts persist after migration, consider switching from `dataTables.bootstrap4.min.css` to `dataTables.dataTables.min.css` (vanilla DataTables CSS) with pure Tailwind overrides.

### 5.7 Select2 Styling in Tailwind Context

Select2 with the `bootstrap4` theme works alongside Tailwind. The `.select2-container--bootstrap4` CSS selectors in our overrides (Section 1.4) ensure the dropdowns look consistent. No changes needed to Select2 initialization code.

---

## 6. Dark Mode Setup

### 6.1 Tailwind dark: Variant

The config already includes `darkMode: 'class'`. To enable dark mode, add a `dark` class to `<html>` or `<body>`.

### 6.2 CSS Variables for Dark Mode

Add to the `:root` style block:

```css
.dark {
    --color-background: #111827;
    --color-surface: #1f2937;
    --color-text: #f3f4f6;
    --color-muted: #9ca3af;
}
```

### 6.3 Usage Pattern

For any element that should change in dark mode:
```html
<div class="bg-white dark:bg-gray-800 text-gray-800 dark:text-gray-200">
```

### 6.4 Dark Mode Toggle (Optional)

Add a toggle button in the topbar:
```html
<button onclick="document.documentElement.classList.toggle('dark')"
        class="text-white hover:bg-white/10 rounded-lg p-2">
    <i class="fa-solid fa-moon"></i>
</button>
```

Save preference to `localStorage`:
```javascript
// In header.php
if (localStorage.getItem('darkMode') === 'true') {
    document.documentElement.classList.add('dark');
}
```

> **Note**: Dark mode is an enhancement for later. The initial migration should focus on light mode only. Dark mode can be added incrementally by adding `dark:` variants to the converted Tailwind classes.

---

## 7. Edge Cases & Gotchas

### 7.1 DataTables + Tailwind Styling Conflicts

**Problem**: DataTables BS4 CSS expects Bootstrap grid classes. With AdminLTE CSS removed, some DataTable controls may look unstyled.

**Solution**: Keep `dataTables.bootstrap4.min.css` AND add the DataTables override CSS from Section 1.4. The Tailwind `@apply` directives override Bootstrap defaults. If controls still look off, add:

```css
/* Force DataTables controls to use Tailwind styling */
.dataTables_wrapper { @apply text-sm; }
.dataTables_wrapper .row { @apply flex flex-wrap items-center justify-between gap-4; }
.dataTables_wrapper .col-sm-12 { @apply w-full; }
.dataTables_wrapper .col-sm-12.col-md-6 { @apply w-full md:w-1/2; }
```

### 7.2 Select2 in Preline Modals

**Problem**: Select2 dropdowns inside Bootstrap modals use `dropdownParent: $(this).closest('.modal')` to render correctly. With Preline overlays, `.modal` class no longer exists.

**Solution**: Change `dropdownParent` in ourjs.php and all page-specific initializations:

```javascript
// BEFORE (Bootstrap):
$('.select2For_modal').each(function() {
    $(this).select2({
        theme: 'bootstrap4',
        dropdownParent: $(this).closest('.modal')
    });
});

// AFTER (Preline):
$('.select2For_modal').each(function() {
    $(this).select2({
        theme: 'bootstrap4',
        dropdownParent: $(this).closest('[role="dialog"]')
    });
});
```

Also, re-initialize Select2 after Preline opens the overlay:

```javascript
document.querySelectorAll('[role="dialog"]').forEach(function(overlay) {
    overlay.addEventListener('open.hs.overlay', function() {
        $(this).find('.select2For_modal').each(function() {
            if (!$(this).data('select2')) {
                $(this).select2({
                    theme: 'bootstrap4',
                    dropdownParent: $(this).closest('[role="dialog"]')
                });
            }
        });
    });
});
```

### 7.3 Leaflet Map Invalidation on Preline Overlay Open

**Problem**: Leaflet maps inside modals render incorrectly (gray tiles, half-loaded) because the container has `display: none` when the map initializes.

**Solution**: The existing code already handles this with `$('#modal').on('shown.bs.modal', ...)`. Change to Preline event:

```javascript
// BEFORE:
$('#modal').on('shown.bs.modal', function () {
    setTimeout(function() {
        map.invalidateSize();
    }, 300);
});

// AFTER:
document.querySelector('#modal').addEventListener('open.hs.overlay', function () {
    setTimeout(function() {
        if (window.currentMap) {
            window.currentMap.invalidateSize();
        }
    }, 400); // Slightly longer delay for Preline transition
});
```

### 7.4 Preline Auto-Init After AJAX Content

**Problem**: Preline components loaded via AJAX (like `.load()` for history modals) won't be initialized.

**Solution**: Call `HSStaticMethods.autoInit()` after AJAX completes:

```javascript
// After any .load() or AJAX that adds Preline-attributed HTML:
$('#modal_history_cancel_job .body_detail_history_cancel_job').load(url, function() {
    if (window.HSStaticMethods) {
        window.HSStaticMethods.autoInit();
    }
});
```

### 7.5 jQuery `$(...).modal()` Global Search-Replace

These are ALL the modal API calls in the codebase that must change:

| File | Line Pattern | Change To |
|---|---|---|
| `ourjs.php` | `$('#modal-add').modal('hide')` | `HSOverlay.close(document.querySelector('#modal-add'))` |
| `page_job.php` | `modal.modal('show')` | `HSOverlay.open(modalEl)` (where `let modalEl = document.querySelector('#modal')`) |
| `page_job.php` | `$('#modal').modal('show')` | `HSOverlay.open(document.querySelector('#modal'))` |
| `page_job.php` | `$('#modal_delete').modal('show')` | `HSOverlay.open(document.querySelector('#modal_delete'))` |
| `page_job.php` | `$('#modal_detail').modal('show')` | `HSOverlay.open(document.querySelector('#modal_detail'))` |
| `page_job.php` | `$('#modal_camera').modal('show')` | `HSOverlay.open(document.querySelector('#modal_camera'))` |
| `page_job.php` | `$('#modal_history_cancel_job').modal('show')` | `HSOverlay.open(document.querySelector('#modal_history_cancel_job'))` |
| `page_user.php` | `modal.modal('show')` | Same pattern |
| `page_user.php` | `modalDelete.modal('show')` | Same pattern |
| `page_company.php` | `modal.modal('show')` | Same pattern |
| `page_company.php` | `modalDelete.modal('show')` | Same pattern |
| `page_company.php` | `$('#modal_update_profile').modal('show')` | Same pattern |
| `page_customer.php` | `modal.modal('show')` | Same pattern |
| `page_customer.php` | `modalDelete.modal('show')` | Same pattern |
| `page_reschedule_job.php` | Various modal calls | Same pattern |

**Helper function** (add to ourjs.php to ease migration):

```javascript
// Modal compatibility helpers (add to ourjs.php)
function showModal(selector) {
    const el = document.querySelector(selector);
    if (el && typeof HSOverlay !== 'undefined') {
        HSOverlay.open(el);
    }
}

function hideModal(selector) {
    const el = document.querySelector(selector);
    if (el && typeof HSOverlay !== 'undefined') {
        HSOverlay.close(el);
    }
}
```

Then replace `$('#modal').modal('show')` with `showModal('#modal')` and `$('#modal').modal('hide')` with `hideModal('#modal')` throughout.

### 7.6 AdminLTE `data-widget` Attributes

Remove these attributes during conversion (Preline handles their functionality):
- `data-widget="pushmenu"` -> Replaced by `data-hs-overlay="#sidebar"` on hamburger button
- `data-widget="treeview"` -> Replaced by Preline accordion
- `data-card-widget="collapse"` -> Replaced by `data-hs-collapse`

### 7.7 Flash Message Display

The current flash message system uses `$this->session->flashdata('message')` which outputs raw HTML like `<div class="alert alert-success">`. Two approaches:

**Option A**: Update the controller to output Tailwind-styled alerts:
```php
$this->session->set_flashdata('message', '<div class="rounded-lg bg-green-50 border border-green-200 p-3 text-sm text-green-800 alert-auto-dismiss">Success!</div>');
```

**Option B**: Add CSS that maps old Bootstrap alert classes to Tailwind (transitional):
```css
.alert-success { @apply rounded-lg bg-green-50 border border-green-200 px-4 py-2 text-sm text-green-800 inline-block; animation: fadeInOut 6s forwards; }
.alert-danger { @apply rounded-lg bg-red-50 border border-red-200 px-4 py-2 text-sm text-red-800 inline-block; animation: fadeInOut 6s forwards; }
.alert-warning { @apply rounded-lg bg-amber-50 border border-amber-200 px-4 py-2 text-sm text-amber-800 inline-block; animation: fadeInOut 6s forwards; }
.alert-info { @apply rounded-lg bg-cyan-50 border border-cyan-200 px-4 py-2 text-sm text-cyan-800 inline-block; animation: fadeInOut 6s forwards; }
```

**Recommendation**: Use Option B initially (less risk), then migrate to Option A when updating controllers.

### 7.8 Summernote Editor

Summernote depends on Bootstrap CSS for its toolbar styling. Since we keep `summernote-bs4.min.css`, the editor should work. However, without `bootstrap.bundle.min.js`, Summernote's dropdown menus (font, color picker) may break.

**Solution**: Include a minimal Bootstrap JS bundle just for Summernote, or use the standalone Summernote lite version:

```html
<!-- Option: Summernote Lite (no Bootstrap dependency) -->
<!-- Replace summernote-bs4.min.css with summernote-lite.min.css -->
<!-- Replace summernote-bs4.min.js with summernote-lite.min.js -->
```

If Summernote is not actively used on any page, it can be commented out entirely and re-added when needed.

### 7.9 sidebar-collapse Class

AdminLTE uses `sidebar-collapse` on `<body>` to toggle sidebar state. This is no longer needed with Preline offcanvas. The sidebar is controlled by `hs-overlay` which handles responsive show/hide automatically.

### 7.10 z-index Stacking

Preline overlays use `z-[80]` for modals. Ensure:
- Sidebar: `z-[60]`
- Topbar: `z-50`
- Modal overlays: `z-[80]`
- SweetAlert2: Already uses `z-index: 10000+` (no conflict)
- Select2 dropdowns: Already uses `z-index: 9999` (no conflict)
- Leaflet/OpenLayers: Stay within their containers (no conflict)

---

## 8. Test Plan

After converting each phase, manually verify:

### Phase A (Layout Shell)
- [ ] Page loads without console errors
- [ ] Sidebar visible on desktop (lg+), hidden on mobile
- [ ] Hamburger button toggles sidebar on mobile
- [ ] Sidebar accordion (Job List, Report) expands/collapses
- [ ] Active nav link is highlighted with primary color
- [ ] Real-time job counter updates in sidebar every 5 seconds
- [ ] Bell shake animation on reschedule notification
- [ ] Sign Out button triggers SweetAlert confirmation
- [ ] Footer visible at bottom
- [ ] Topbar stays fixed on scroll

### Phase B (Auth Pages)
- [ ] Login page renders correctly (centered card, gradient button)
- [ ] Email validation shows inline error
- [ ] Password validation shows inline error
- [ ] Successful login redirects to dashboard
- [ ] Flash messages display on validation failure

### Phase C (Dashboard & Simple Pages)
- [ ] Dashboard info-boxes display correct numbers
- [ ] Dashboard tables render (DataTables initialized)
- [ ] Card collapse buttons work (Preline collapse)
- [ ] User page: Add/Edit/Delete modals open and close
- [ ] User page: Select2 works inside modal (company dropdown)
- [ ] User page: Import Excel triggers upload
- [ ] User page: Form validation shows inline errors
- [ ] Company page: All 3 modals work (Add/Edit, Delete, Update Profile)
- [ ] Company page: File upload with preview works
- [ ] Company page: Password toggle (eye icon) works
- [ ] Customer page: Leaflet map renders inside modal
- [ ] Customer page: Map click updates coordinates
- [ ] Customer page: Geocoder search works

### Phase D (Complex Pages)
- [ ] Job page: 4 info-box cards update via AJAX
- [ ] Job page: DataTable loads via server-side AJAX
- [ ] Job page: DataTable auto-refreshes every 5 seconds
- [ ] Job page: Add Job modal opens with Leaflet map
- [ ] Job page: Edit Job populates form via AJAX
- [ ] Job page: Delete Job confirmation modal works
- [ ] Job page: Detail Job modal shows status badge with correct color
- [ ] Job page: Camera modal shows job photos
- [ ] Job page: Cancel History modal loads via `.load()`
- [ ] Job page: Status badges render correctly in DataTable (ongoing, finished, awaiting, reschedule)
- [ ] Reschedule page: Table loads, approve/reject actions work

### Phase E (Maps & Vehicle)
- [ ] Map page: OpenLayers map renders correctly
- [ ] Map page: Vehicle markers display
- [ ] Map page: Zoom controls work
- [ ] Map page: Search box works
- [ ] Map page: Hover tooltips display
- [ ] Vehicle page: DataTable loads via AJAX
- [ ] Vehicle page: Loading spinner shows then hides

### Phase F (Reports)
- [ ] Report pages: DataTables with export buttons render
- [ ] Excel export works
- [ ] PDF export works
- [ ] Print button works
- [ ] Column visibility toggle works
- [ ] Date range filters work
- [ ] Detail pages render correctly

### Phase G (Standalone Pages)
- [ ] Forgot password page renders (standalone styling)
- [ ] Password match validation works
- [ ] Success/expired pages render

### Cross-Cutting Concerns
- [ ] SweetAlert2 toasts display correctly (top-right position)
- [ ] SweetAlert2 confirmation dialogs work (delete, logout)
- [ ] SweetAlert2 loading overlay works with blur effect
- [ ] Flash messages display and auto-dismiss after 6 seconds
- [ ] All AJAX CRUD operations work (create, update, delete)
- [ ] File imports (Excel) work correctly
- [ ] Image preview works on file select
- [ ] Notification sound plays on reschedule alert
- [ ] Print stylesheet hides sidebar/topbar (`.no-print`)
- [ ] Responsive: All pages work on mobile viewport
- [ ] No horizontal scrollbar on any page (except DataTables with `scrollX`)

---

## Appendix: Quick Reference — Class Mapping Cheat Sheet

```
LAYOUT:
  content-wrapper        -> (remove, handled by <main> in index.php)
  content-header         -> px-4 sm:px-6 lg:px-8 py-4
  content                -> px-4 sm:px-6 lg:px-8 pb-6
  container-fluid        -> max-w-full (or remove)

CARDS:
  card                   -> bg-white rounded-xl shadow-sm border border-gray-200
  card-header            -> px-5 py-4 border-b border-gray-200
  card-title             -> text-base font-semibold text-gray-800
  card-body              -> px-5 py-4
  card-footer            -> px-5 py-3 border-t border-gray-200 bg-gray-50 rounded-b-xl

BUTTONS:
  btn btn-primary btn-sm -> inline-flex items-center gap-1.5 rounded-lg bg-primary px-3 py-1.5 text-sm font-medium text-white hover:bg-primary-light
  btn btn-danger btn-sm  -> ... bg-destructive ... hover:bg-red-700
  btn btn-success btn-sm -> ... bg-green-600 ... hover:bg-green-700
  btn btn-warning btn-sm -> ... bg-amber-500 ... hover:bg-amber-600
  btn btn-info btn-sm    -> ... bg-cyan-600 ... hover:bg-cyan-700
  btn-block              -> add w-full justify-center

FORMS:
  form-group             -> mb-4
  form-control           -> tw-input block w-full rounded-lg border border-gray-300 px-3 py-2 text-sm ...
  input-group            -> flex
  input-group-prepend    -> (span with rounded-l-lg border ...)
  input-group-append     -> (span with rounded-r-lg border ...)

TABLES:
  table-responsive       -> overflow-x-auto
  table table-bordered   -> w-full text-sm (+ DataTables override CSS)

MODALS:
  modal fade             -> hs-overlay hidden fixed inset-0 z-[80] overflow-x-hidden overflow-y-auto
  modal-dialog modal-lg  -> hs-overlay-open:mt-7 ... sm:max-w-2xl sm:w-full m-3 sm:mx-auto
  modal-content          -> flex flex-col bg-white border shadow-lg rounded-xl
  modal-header           -> flex items-center justify-between px-5 py-4 border-b
  modal-body             -> px-5 py-4 overflow-y-auto max-h-[70vh]
  modal-footer           -> flex items-center justify-end gap-2 px-5 py-3 border-t
  data-toggle="modal"    -> data-hs-overlay="#id"
  data-dismiss="modal"   -> data-hs-overlay="#id"
  .modal('show')         -> HSOverlay.open(el) / showModal('#id')
  .modal('hide')         -> HSOverlay.close(el) / hideModal('#id')
  shown.bs.modal         -> open.hs.overlay
  hidden.bs.modal        -> close.hs.overlay

GRID:
  row                    -> grid grid-cols-12 gap-4  (or flex flex-wrap gap-4)
  col-md-6               -> md:col-span-6  (or use grid-cols-2)
  col-md-4               -> md:col-span-4  (or use grid-cols-3)
  col-md-3               -> md:col-span-3  (or use grid-cols-4)

UTILITY:
  d-flex                 -> flex
  d-none                 -> hidden
  justify-content-between -> justify-between
  align-items-center     -> items-center
  text-center            -> text-center
  mb-3                   -> mb-3
  mt-3                   -> mt-3
  w-100                  -> w-full
  text-danger            -> text-red-600
  text-success           -> text-green-600
  font-weight-bold       -> font-bold
  elevation-1            -> shadow-sm
  img-circle             -> rounded-full
```

---

*End of Migration Guide*
