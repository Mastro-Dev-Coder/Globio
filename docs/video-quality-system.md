# Sistema Qualità Video

## Panoramica

Il sistema qualità video permette di gestire e visualizzare diverse qualità di riproduzione per ogni video, integrato con Artplayer.

## Architettura

### Componenti Principali

1. **Modello Video** (`app/Models/Video.php`)
   - Gestisce la logica di rilevamento delle qualità
   - Restituisce le qualità nel formato richiesto da Artplayer

2. **Modello VideoQuality** (`app/Models/VideoQuality.php`)
   - Memorizza le informazioni sulle qualità video nel database
   - Fornisce metodi per verificare l'esistenza dei file

3. **Componente Blade** (`resources/views/components/video-player-with-ads.blade.php`)
   - Passa le qualità al player tramite data attributes

4. **JavaScript** (`resources/js/artplayer-init.js`)
   - Inizializza Artplayer con le qualità disponibili

## Formato dei Dati

### Struttura Restituita dal Backend

Il metodo `getAvailableQualities()` restituisce un array di oggetti con il seguente formato:

```php
[
    [
        'default' => true,           // Boolean: true per la qualità di default
        'html' => '1080p Full HD',   // String: etichetta visualizzata nel player
        'url' => 'https://...',      // String: URL del file video
        'quality' => '1080p',        // String: identificativo della qualità
        'id' => 123,                 // Integer|null: ID del record VideoQuality
        'file_size' => 1048576,      // Integer|null: dimensione in bytes
        'formatted_file_size' => '1 MB', // String: dimensione formattata
        'width' => 1920,             // Integer|null: larghezza in pixel
        'height' => 1080,            // Integer|null: altezza in pixel
        'resolution' => '1920x1080', // String|null: risoluzione
        'bitrate' => 5000,           // Integer|null: bitrate in kbps
        'is_fallback' => false       // Boolean: true se è fallback
    ],
    // ... altre qualità
]
```

### Qualità Supportate

Il sistema supporta le seguenti qualità (ordinate dalla più alta alla più bassa):

- `original` - Originale (qualità del file caricato)
- `2160p` - 2160p 4K Ultra HD (3840x2160)
- `1440p` - 1440p 2K QHD (2560x1440)
- `1080p` - 1080p Full HD (1920x1080)
- `720p` - 720p HD (1280x720)
- `480p` - 480p (854x480)
- `360p` - 360p (640x360)

## Funzionamento

### 1. Rilevamento delle Qualità

Il sistema rileva le qualità in due modi:

#### Dal Database (Priorità)

Se esistono record nella tabella `video_qualities`:

```php
$qualities = $video->videoQualities()
    ->available()
    ->byQuality()
    ->get()
    ->filter(function ($quality) {
        return $quality->fileExists();
    });
```

#### Dai File Fisici (Fallback)

Se non ci sono record nel database, cerca i file nella cartella `public/storage/videos/`:

```php
// Pattern: video_{video_id}_{quality}.mp4
// Esempio: video_33_1080p.mp4
```

**Nota importante:** Il sistema usa `public_path()` per accedere ai file video, non `Storage::disk('public')`. Questo perché i file sono memorizzati in `public/storage/videos/` e non in `storage/app/public/`.

### 2. Garanzia di Qualità Minima

Il sistema garantisce sempre almeno una qualità disponibile:

- Se ci sono qualità nel database, le usa
- Se non ci sono qualità nel database, cerca i file fisici
- Se non trova nulla, usa il file originale come fallback

### 3. Passaggio al Player

Le qualità vengono passate al player tramite data attribute:

```blade
<div id="artplayer"
    data-qualities="{!! json_encode($video->getAvailableQualities()) !!}"
    ...>
</div>
```

### 4. Inizializzazione del Player

Il JavaScript riceve le qualità e le passa direttamente ad Artplayer:

```javascript
const qualities = JSON.parse(container.dataset.qualities);

defaultOptions.quality = qualities;
```

## Utilizzo

### Nel Controller

```php
public function show($videoUrl)
{
    $video = Video::where('video_url', $videoUrl)->firstOrFail();
    
    // Ottieni le qualità disponibili
    $qualities = $video->getAvailableQualities();
    
    return view('video.show', compact('video', 'qualities'));
}
```

### Nella View Blade

```blade
<x-video-player-with-ads :video="$video" />
```

### Creazione Manuale delle Qualità

```php
// Crea una qualità per un video
VideoQuality::createForVideo($video, '1080p', [
    'file_path' => 'videos/video_33_1080p.mp4',
    'file_size' => 104857600,
    'width' => 1920,
    'height' => 1080,
    'bitrate' => 5000,
    'is_available' => true,
]);
```

### Salvataggio Massivo delle Qualità

```php
// Salva tutte le qualità dopo il transcoding
$video->saveVideoQualities([
    '1080p' => [
        'file_path' => 'videos/video_33_1080p.mp4',
        'file_size' => 104857600,
        'width' => 1920,
        'height' => 1080,
        'bitrate' => 5000,
    ],
    '720p' => [
        'file_path' => 'videos/video_33_720p.mp4',
        'file_size' => 52428800,
        'width' => 1280,
        'height' => 720,
        'bitrate' => 2500,
    ],
]);
```

## Database

### Tabella `video_qualities`

```php
Schema::create('video_qualities', function (Blueprint $table) {
    $table->id();
    $table->foreignId('video_id')->constrained()->onDelete('cascade');
    $table->string('quality'); // original, 2160p, 1440p, 1080p, 720p, 480p, 360p
    $table->string('label'); // Etichetta visualizzata
    $table->string('file_path');
    $table->string('file_url')->nullable();
    $table->bigInteger('file_size')->nullable();
    $table->integer('width')->nullable();
    $table->integer('height')->nullable();
    $table->integer('bitrate')->nullable();
    $table->boolean('is_available')->default(true);
    $table->timestamps();
    
    $table->index(['video_id', 'quality']);
    $table->index('is_available');
});
```

## Struttura dei File

### Cartella Videos

```
storage/videos/
├── video_33_original.mp4
├── video_33_1080p.mp4
├── video_33_720p.mp4
├── video_33_480p.mp4
└── video_33_360p.mp4
```

### Cartella Sottotitoli

```
storage/subtitles/
├── video_33_it.vtt
├── video_33_en.vtt
└── video_33_es.vtt
```

## Debug

### Log Console

Il sistema logga diverse informazioni per il debug:

```javascript
// Nel browser console
console.log('Valore grezzo data-qualities:', container.dataset.qualities);
console.log('qualities parsati:', qualities);
console.log('Qualità configurate:', defaultOptions.quality);
```

### Verifica delle Qualità

```php
// In un controller o tinker
$video = Video::find(33);
$qualities = $video->getAvailableQualities();

dd($qualities);
```

### Verifica dei File

```php
// Verifica se un file esiste
$exists = Storage::disk('public')->exists('videos/video_33_1080p.mp4');

// Ottieni la dimensione del file
$size = Storage::disk('public')->size('videos/video_33_1080p.mp4');
```

## Best Practices

### 1. Ordinamento delle Qualità

Le qualità sono sempre ordinate dalla più alta alla più bassa per offrire la migliore esperienza utente.

### 2. Verifica dell'Esistenza dei File

Il sistema verifica sempre che i file esistano fisicamente prima di includerli nelle qualità disponibili.

### 3. Fallback

Il sistema ha sempre un fallback alla qualità originale per garantire che il video sia sempre riproducibile.

### 4. Performance

- Usa il database per memorizzare le qualità quando possibile
- Evita di scansionare il filesystem ad ogni richiesta
- Cache le informazioni sulle qualità se necessario

### 5. Sicurezza

- Usa `Storage::disk('public')->url()` per generare gli URL
- Non esporre percorsi di file system diretti
- Verifica sempre l'esistenza dei file prima di usarli

## Troubleshooting

### Problema: Le qualità non appaiono nel player

**Soluzione:**
1. Verifica che i file esistano nella cartella `storage/videos/`
2. Controlla i log della console del browser
3. Verifica che il formato dei dati sia corretto

### Problema: Il video non si riproduce

**Soluzione:**
1. Verifica che almeno una qualità sia disponibile
2. Controlla che gli URL siano corretti
3. Verifica i permessi dei file

### Problema: La qualità di default non è quella corretta

**Soluzione:**
1. Verifica che il flag `default` sia impostato correttamente
2. Controlla l'ordinamento delle qualità
3. Assicurati che la qualità di default sia la prima nell'array

## Esempi Completi

### Esempio 1: Video con Qualità dal Database

```php
// Creazione del video
$video = Video::create([
    'title' => 'Il mio video',
    'video_path' => 'videos/video_33_original.mp4',
    'thumbnail_path' => 'thumbnails/video_33.jpg',
    'status' => 'published',
]);

// Creazione delle qualità
$video->saveVideoQualities([
    '1080p' => [
        'file_path' => 'videos/video_33_1080p.mp4',
        'file_size' => 104857600,
        'width' => 1920,
        'height' => 1080,
        'bitrate' => 5000,
    ],
    '720p' => [
        'file_path' => 'videos/video_33_720p.mp4',
        'file_size' => 52428800,
        'width' => 1280,
        'height' => 720,
        'bitrate' => 2500,
    ],
]);

// Utilizzo nella view
<x-video-player-with-ads :video="$video" />
```

### Esempio 2: Video con Qualità dai File

```php
// Creazione del video
$video = Video::create([
    'title' => 'Il mio video',
    'video_path' => 'videos/video_33_original.mp4',
    'thumbnail_path' => 'thumbnails/video_33.jpg',
    'status' => 'published',
]);

// I file delle qualità sono già presenti in storage/videos/
// Il sistema li rileverà automaticamente

// Utilizzo nella view
<x-video-player-with-ads :video="$video" />
```

## Note Importanti

1. **Compatibilità Artplayer**: Il formato dei dati è specifico per Artplayer e non deve essere modificato senza aggiornare anche il JavaScript.

2. **Performance**: Per video con molte qualità, considera di cacheare i risultati di `getAvailableQualities()`.

3. **Transcoding**: Il sistema non include funzionalità di transcoding. Devi usare un sistema esterno (es. FFmpeg) per generare le diverse qualità.

4. **Storage**: Assicurati di avere abbastanza spazio su disco per memorizzare tutte le qualità.

5. **Backup**: Fai regolarmente il backup dei file video e del database delle qualità.

## Supporto

Per problemi o domande sul sistema qualità video, contatta il team di sviluppo o consulta la documentazione di Artplayer.