@props([
    'label' => 'Premium',
    'size' => 'md',
    'variant' => 'solid',
])

@php
    $sizeClasses = match ($size) {
        'sm' => 'gap-1.5 px-2.5 py-1 text-[11px] leading-none',
        'lg' => 'gap-2.5 px-4 py-2 text-sm',
        default => 'gap-2 px-3 py-1.5 text-xs',
    };

    $iconClasses = match ($size) {
        'sm' => 'text-[10px]',
        'lg' => 'text-sm',
        default => 'text-[11px]',
    };

    $variantClasses = match ($variant) {
        'soft' => 'border border-amber-200 bg-amber-50 text-amber-800 shadow-sm shadow-amber-100/70 dark:border-amber-400/30 dark:bg-amber-300/12 dark:text-amber-100 dark:shadow-black/20',
        'outline' => 'border border-amber-300/80 bg-white text-amber-800 shadow-sm shadow-amber-100/60 dark:border-amber-400/35 dark:bg-slate-900/70 dark:text-amber-100 dark:shadow-black/20',
        default => 'border border-amber-300/75 bg-gradient-to-r from-amber-300 via-yellow-200 to-amber-100 text-amber-950 shadow-md shadow-amber-200/70 dark:border-amber-300/30 dark:from-amber-300 dark:via-yellow-200 dark:to-amber-100 dark:text-amber-950 dark:shadow-amber-950/30',
    };
@endphp

<span
    {{ $attributes->class([
        'inline-flex items-center rounded-full font-semibold uppercase tracking-[0.18em] backdrop-blur-sm',
        $sizeClasses,
        $variantClasses,
    ]) }}>
    <i class="fas fa-crown {{ $iconClasses }}"></i>
    <span>{{ $label }}</span>
</span>
