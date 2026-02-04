@props(['position' => '', 'limit' => 1])

@php
use App\Helpers\AdvertisementHelper;

$advertisements = AdvertisementHelper::getActiveAdvertisements($position, $limit);
@endphp

@if($advertisements->count() > 0)
    <div class="advertisement-container advertisement-{{ $position }}">
        @foreach($advertisements as $advertisement)
            <div class="advertisement-item advertisement-type-{{ $advertisement->type }} mb-4">
                {!! AdvertisementHelper::renderAdvertisement($advertisement) !!}
            </div>
        @endforeach
    </div>
    
    <!-- Script per il tracking dei click -->
    {!! AdvertisementHelper::generateClickTrackingScript() !!}
@endif

<style>
.advertisement-container {
    margin: 1rem 0;
}

.advertisement-header {
    text-align: center;
    margin-bottom: 0.5rem;
}

.advertisement-sidebar {
    margin: 1rem 0;
}

.advertisement-footer {
    margin: 1rem 0;
    text-align: center;
}

.advertisement-between-videos {
    margin: 2rem 0;
    padding: 1rem;
    background-color: #f9f9f9;
    border-radius: 8px;
}

.advertisement-video-overlay {
    position: relative;
    margin: 1rem 0;
}

.advertisement-banner img {
    max-width: 100%;
    height: auto;
    border-radius: 4px;
}

.advertisement-adsense,
.advertisement-video {
    text-align: center;
}
</style>