@php
    use App\Helpers\AdvertisementHelper;
    
    $position = $position ?? 'header';
    $limit = $limit ?? 1;
    
    // Ottiene le pubblicità attive per la posizione specificata
    $advertisements = AdvertisementHelper::getActiveAdvertisements($position, $limit);
@endphp

@if($advertisements && $advertisements->count() > 0)
    <div class="advertisements-container advertisements-{{ $position }}">
        @foreach($advertisements as $advertisement)
            {!! AdvertisementHelper::renderAdvertisement($advertisement) !!}
        @endforeach
    </div>
@endif

<style>
    .advertisements-container {
        margin: 1rem 0;
    }
    
    .advertisements-footer {
        text-align: center;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 1px solid #e5e7eb;
    }
    
    .advertisements-between_videos {
        margin: 2rem 0;
        text-align: center;
        padding: 1rem;
        background: #f9fafb;
        border-radius: 8px;
    }
    
    .advertisements-home_video {
        margin: 2rem 0;
        text-align: center;
        padding: 1.5rem;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(102, 126, 234, 0.3);
    }
    
    .advertisements-video_overlay {
        position: relative;
    }
    
    /* Responsive per dispositivi mobili */
    @media (max-width: 768px) {
        .advertisements-between_videos {
            margin: 1rem 0;
            padding: 0.5rem;
        }
    }
</style>