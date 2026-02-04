@extends('layouts.app')

@section('title', 'Segnala Contenuto')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Header -->
            <div class="mb-4">
                <h1 class="h3 mb-2 text-gray-800">
                    <i class="fas fa-flag text-danger"></i>
                    Segnala {{ $targetType }}
                </h1>
                <p class="text-muted">Aiutaci a mantenere la community sicura segnalando contenuti che violano le linee guida.</p>
            </div>

            <!-- Card principale -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-exclamation-circle"></i>
                        Dettagli della segnalazione
                    </h6>
                </div>
                <div class="card-body">
                    <!-- Anteprima del contenuto da segnalare -->
                    <div class="alert alert-info mb-4">
                        <div class="d-flex align-items-center">
                            @if($type === 'video' && isset($target->thumbnail))
                                <img src="{{ asset('storage/' . $target->thumbnail) }}" 
                                     class="rounded mr-3" style="width: 120px; height: 68px; object-fit: cover;" 
                                     alt="Thumbnail">
                            @elseif($type === 'channel' && isset($target->userProfile->avatar))
                                <img src="{{ asset('storage/' . $target->userProfile->avatar) }}" 
                                     class="rounded-circle mr-3" style="width: 60px; height: 60px; object-fit: cover;" 
                                     alt="Avatar">
                            @elseif($type === 'comment')
                                <div class="bg-primary text-white rounded p-3 mr-3">
                                    <i class="fas fa-comment fa-2x"></i>
                                </div>
                            @endif
                            
                            <div>
                                <h6 class="mb-1">
                                    @if($type === 'video')
                                        {{ $target->title }}
                                    @elseif($type === 'channel')
                                        {{ $target->name }}
                                    @elseif($type === 'comment')
                                        Commento di {{ $target->user->name }}
                                    @else
                                        {{ $target->name }}
                                    @endif
                                </h6>
                                <small class="text-muted">
                                    @if($type === 'video')
                                        Di {{ $target->user->name }} • {{ number_format($target->views) }} visualizzazioni
                                    @elseif($type === 'channel')
                                        {{ '@' . ($target->userProfile->username ?? $target->name) }}
                                    @elseif($type === 'comment')
                                        {{ $target->created_at->diffForHumans() }}
                                    @else
                                        {{ $target->email }}
                                    @endif
                                </small>
                            </div>
                        </div>
                    </div>

                    <!-- Form di segnalazione -->
                    <form method="POST" action="{{ route('reports.store') }}">
                        @csrf
                        
                        <input type="hidden" name="type" value="{{ $type }}">
                        <input type="hidden" name="target_id" value="{{ $target->id }}">

                        <!-- Selezione motivo -->
                        <div class="mb-4">
                            <label class="form-label font-weight-bold">
                                <i class="fas fa-list-ul text-primary mr-2"></i>
                                Perché stai segnalando questo contenuto?
                            </label>
                            
                            <div class="row" id="presetReasons">
                                @foreach($presetReasons as $index => $reason)
                                    <div class="col-md-6 mb-2">
                                        <div class="custom-control custom-radio">
                                            <input type="radio" 
                                                   class="custom-control-input reason-radio" 
                                                   id="reason_{{ $reason['value'] }}" 
                                                   name="report_type" 
                                                   value="{{ $reason['value'] }}"
                                                   {{ $index === 0 ? 'checked' : '' }}
                                                   onchange="toggleCustomReason()">
                                            <label class="custom-control-label w-100 p-3 border rounded cursor-pointer hover-bg-light" 
                                                   for="reason_{{ $reason['value'] }}">
                                                <i class="fas {{ $reason['icon'] }} text-secondary mr-2"></i>
                                                {{ $reason['label'] }}
                                            </label>
                                        </div>
                                        @if($loop->last)
                                            <input type="radio" 
                                                   class="custom-control-input reason-radio" 
                                                   id="reason_custom" 
                                                   name="report_type" 
                                                   value="custom"
                                                   onchange="toggleCustomReason()">
                                            <label class="custom-control-label w-100 p-3 border rounded cursor-pointer hover-bg-light" 
                                                   for="reason_custom">
                                                <i class="fas fa-pen text-secondary mr-2"></i>
                                                Altro (specifica)
                                            </label>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            
                            @error('report_type')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Campo motivo personalizzato -->
                        <div class="mb-4" id="customReasonSection" style="display: none;">
                            <label for="custom_reason" class="form-label font-weight-bold">
                                <i class="fas fa-pen text-primary mr-2"></i>
                                Specifica il motivo
                            </label>
                            <input type="text" 
                                   class="form-control @error('custom_reason') is-invalid @enderror" 
                                   id="custom_reason" 
                                   name="custom_reason" 
                                   placeholder="Descrivi brevemente il motivo della segnalazione"
                                   maxlength="255">
                            @error('custom_reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Motivo standard (hidden field per compatibilità) -->
                        <div class="mb-4">
                            <label for="reason" class="form-label font-weight-bold">
                                <i class="fas fa-tag text-primary mr-2"></i>
                                Categoria della segnalazione
                            </label>
                            <select class="form-control @error('reason') is-invalid @enderror" 
                                    id="reason" 
                                    name="reason"
                                    onchange="updateReportType()">
                                <option value="spam">Spam</option>
                                <option value="harassment">Molestie/Bullismo</option>
                                <option value="inappropriate_content">Contenuto inappropriato</option>
                                <option value="copyright">Violazione copyright</option>
                                <option value="fake_information">Informazioni false</option>
                                <option value="other">Altro</option>
                            </select>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descrizione dettagliata -->
                        <div class="mb-4">
                            <label for="description" class="form-label font-weight-bold">
                                <i class="fas fa-align-left text-primary mr-2"></i>
                                Descrizione aggiuntiva (opzionale)
                            </label>
                            <textarea class="form-control @error('description') is-invalid @enderrors" 
                                      id="description" 
                                      name="description" 
                                      rows="4"
                                      placeholder="Fornisci maggiori dettagli sulla segnalazione per aiutarci a comprendere meglio il problema..."
                                      maxlength="1000"></textarea>
                            <div class="form-text text-muted">
                                <small><span id="charCount">1000</span> caratteri rimanenti</small>
                            </div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Info box -->
                        <div class="alert alert-warning mb-4">
                            <h6 class="alert-heading"><i class="fas fa-info-circle"></i> Come gestiamo le segnalazioni</h6>
                            <ul class="mb-0 small">
                                <li>Il nostro team esaminerà la segnalazione entro 24-48 ore</li>
                                <li>Se il contenuto viola le linee guida, verrà rimosso o nascosto</li>
                                <li>L'utente segnalato riceverà una notifica (se necessario)</li>
                                <li>Le segnalazioni false ripetute possono comportare azioni sul tuo account</li>
                            </ul>
                        </div>

                        <!-- Bottoni -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ url()->previous() }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left mr-2"></i>
                                Annulla
                            </a>
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-flag mr-2"></i>
                                Invia Segnalazione
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Link utili -->
            <div class="text-center">
                <a href="{{ route('terms') }}" class="text-muted small mx-2">Linee guida della community</a>
                <span class="text-muted">•</span>
                <a href="{{ route('privacy') }}" class="text-muted small mx-2">Privacy Policy</a>
                <span class="text-muted">•</span>
                <a href="{{ route('contact') }}" class="text-muted small mx-2">Contattaci</a>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inizializza
    toggleCustomReason();
    updateReportType();
    
    // Contatore caratteri
    const description = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    
    description.addEventListener('input', function() {
        const remaining = 1000 - this.value.length;
        charCount.textContent = remaining;
        charCount.className = remaining < 100 ? 'text-danger' : 'text-muted';
    });
});

// Mostra/nascondi campo motivo personalizzato
function toggleCustomReason() {
    const customRadio = document.getElementById('reason_custom');
    const customSection = document.getElementById('customReasonSection');
    
    if (customRadio && customRadio.checked) {
        customSection.style.display = 'block';
        document.getElementById('custom_reason').required = true;
    } else {
        customSection.style.display = 'none';
        document.getElementById('custom_reason').required = false;
        document.getElementById('custom_reason').value = '';
    }
}

// Aggiorna il campo reason in base alla selezione
function updateReportType() {
    const reasonSelect = document.getElementById('reason');
    const reportTypeRadios = document.querySelectorAll('input[name="report_type"]');
    
    reasonSelect.addEventListener('change', function() {
        reportTypeRadios.forEach(radio => {
            if (radio.value === this.value || (this.value === 'other' && radio.value === 'custom')) {
                radio.checked = true;
            }
        });
        toggleCustomReason();
    });
}

// Gestisci cambio radio
document.querySelectorAll('input[name="report_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        if (this.value !== 'custom') {
            const reasonSelect = document.getElementById('reason');
            if (this.value === 'other') {
                reasonSelect.value = 'other';
            } else {
                reasonSelect.value = this.value;
            }
        }
        toggleCustomReason();
    });
});
</script>

<style>
.reason-radio {
    position: absolute;
    opacity: 0;
}

.custom-control-label {
    cursor: pointer;
    transition: all 0.2s ease;
}

.custom-control-label:hover {
    background-color: #f8f9fa;
    border-color: #007bff;
}

.reason-radio:checked + .custom-control-label {
    background-color: #e7f1ff;
    border-color: #007bff;
    color: #007bff;
}

.hover-bg-light:hover {
    background-color: #f8f9fa;
}
</style>
@endpush
@endsection
