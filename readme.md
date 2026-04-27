# Globio API v1

Base URL: `/api/v1`

Auth header: `Authorization: Bearer <token>`

Formato richieste e risposte: `application/json`

## Panoramica

La API v1 espone autenticazione token-based, contenuti pubblici, playlist, libreria utente e abbonamento `Globio Premium` con Stripe Billing.

Il piano premium e separato dalle iscrizioni ai canali della tabella `subscriptions`.

## Auth

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| POST | `/auth/register` | No | Registra utente e restituisce Bearer token |
| POST | `/auth/login` | No | Login e rilascio Bearer token |
| POST | `/auth/logout` | Si | Revoca il token corrente |

## Premium

`Globio Premium` usa Stripe Checkout per l'acquisto e Stripe Customer Portal per la gestione del rinnovo.

Feature premium:

- `ad_free`
- `background_playback`
- `picture_in_picture`
- `smart_downloads`
- `higher_quality_streaming`
- `reels_enhanced_controls`
- `queue_management`

### Flusso consigliato

1. Chiamare `GET /api/v1/premium/plans`.
2. Autenticare l'utente.
3. Chiamare `POST /api/v1/me/premium/checkout`.
4. Aprire `checkout_url` in browser o webview sicura.
5. Dopo il redirect finale, chiamare `POST /api/v1/me/premium/confirm` con `session_id`.
6. Leggere stato, capabilities e badge da `GET /api/v1/me/premium` o `GET /api/v1/me`.

### Endpoint premium

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/premium/plans` | No | Restituisce il piano premium configurato |
| GET | `/me/premium` | Si | Stato premium, capabilities, badge e scadenze |
| POST | `/me/premium/checkout` | Si | Crea una sessione Stripe Checkout `mode=subscription` |
| POST | `/me/premium/confirm` | Si | Sincronizza la sessione checkout completata |
| POST | `/me/premium/portal` | Si | Crea una sessione Stripe Customer Portal |
| POST | `/me/premium/cancel` | Si | Disattiva il rinnovo automatico a fine periodo |
| POST | `/me/premium/resume` | Si | Riattiva il rinnovo automatico |

### Piano premium

Risposta tipica di `GET /premium/plans`:

```json
{
  "data": [
    {
      "code": "globio-premium",
      "name": "Globio Premium",
      "interval": "month",
      "amount": 1199,
      "currency": "eur",
      "formatted_price": "11,99 EUR",
      "features": {
        "ad_free": true,
        "background_playback": true,
        "picture_in_picture": true,
        "smart_downloads": true,
        "higher_quality_streaming": true,
        "reels_enhanced_controls": true,
        "queue_management": true
      }
    }
  ]
}
```

### Badge premium

Quando l'utente ha un abbonamento attivo, le API restituiscono il badge premium in:

- `GET /me` dentro `premium.badge`
- `GET /me/premium` dentro `badge`

Schema del badge:

```json
{
  "label": "Abbonato Premium",
  "short_label": "Premium",
  "icon": "fa-crown",
  "current_period_end": "2026-05-24T12:00:00+02:00",
  "cancel_at_period_end": false
}
```

Se l'utente non e premium, il badge restituito e `null`.

### Stato premium

Risposta tipica di `GET /me/premium`:

```json
{
  "active": true,
  "plan": {
    "id": 3,
    "plan_code": "globio-premium",
    "plan_name": "Globio Premium",
    "status": "active",
    "billing_interval": "month",
    "amount": 1199,
    "currency": "eur",
    "cancel_at_period_end": false
  },
  "features": {
    "ad_free": true,
    "background_playback": true,
    "picture_in_picture": true,
    "smart_downloads": true,
    "higher_quality_streaming": true,
    "reels_enhanced_controls": true,
    "queue_management": true
  },
  "premium_access_ends_at": "2026-05-24T12:00:00+02:00",
  "badge": {
    "label": "Abbonato Premium",
    "short_label": "Premium",
    "icon": "fa-crown",
    "current_period_end": "2026-05-24T12:00:00+02:00",
    "cancel_at_period_end": false
  },
  "current_period_end": "2026-05-24T12:00:00+02:00"
}
```

### Esempi premium

Checkout:

```bash
curl -X POST "https://example.com/api/v1/me/premium/checkout" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "success_url": "https://example.com/premium/success",
    "cancel_url": "https://example.com/premium/cancel"
  }'
```

Conferma checkout:

```bash
curl -X POST "https://example.com/api/v1/me/premium/confirm" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "session_id": "cs_test_123"
  }'
```

Disattivazione rinnovo:

```bash
curl -X POST "https://example.com/api/v1/me/premium/cancel" \
  -H "Authorization: Bearer TOKEN_VALUE"
```

Riattivazione rinnovo:

```bash
curl -X POST "https://example.com/api/v1/me/premium/resume" \
  -H "Authorization: Bearer TOKEN_VALUE"
```

## Account e badge

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/me` | Si | Profilo utente con blocco `premium`, `badge` e `profile.is_premium_subscriber` |
| PUT | `/me` | Si | Aggiorna nome e dati base del canale |
| GET | `/me/notifications` | Si | Notifiche account |
| POST | `/me/notifications/read-all` | Si | Segna tutte come lette |
| POST | `/me/notifications/{id}/read` | Si | Segna una notifica come letta |

Risposta premium dentro `GET /me`:

```json
{
  "premium": {
    "active": true,
    "premium_access_ends_at": "2026-05-24T12:00:00+02:00",
    "plan": {
      "id": 3,
      "plan_code": "globio-premium",
      "plan_name": "Globio Premium",
      "status": "active",
      "billing_interval": "month",
      "amount": 1199,
      "currency": "eur",
      "current_period_end": "2026-05-24T12:00:00+02:00",
      "cancel_at_period_end": false
    },
    "features": {
      "ad_free": true,
      "background_playback": true,
      "picture_in_picture": true,
      "smart_downloads": true,
      "higher_quality_streaming": true,
      "reels_enhanced_controls": true,
      "queue_management": true
    },
    "badge": {
      "label": "Abbonato Premium",
      "short_label": "Premium",
      "icon": "fa-crown",
      "current_period_end": "2026-05-24T12:00:00+02:00",
      "cancel_at_period_end": false
    }
  },
  "profile": {
    "is_premium_subscriber": true
  }
}
```

## Playlists

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/me/playlists` | Si | Elenco playlist utente |
| POST | `/me/playlists` | Si | Crea una nuova playlist |
| GET | `/me/playlists/{playlist}` | Si | Dettaglio playlist con video |
| PUT | `/me/playlists/{playlist}` | Si | Aggiorna titolo, descrizione, visibilita |
| DELETE | `/me/playlists/{playlist}` | Si | Elimina la playlist |
| POST | `/me/playlists/{playlist}/videos` | Si | Aggiunge un video |
| DELETE | `/me/playlists/{playlist}/videos/{video}` | Si | Rimuove un video |

Esempio creazione playlist:

```bash
curl -X POST "https://example.com/api/v1/me/playlists" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "Reels da rivedere",
    "description": "Playlist personale",
    "is_public": true,
    "video_ids": [12, 18, 22]
  }'
```

## Endpoint core utili per app

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/app-config` | No | Config app, feature flags e piano premium |
| GET | `/home` | No | Feed home |
| GET | `/videos` | No | Lista video o reels |
| GET | `/videos/{id_or_slug}` | No | Dettaglio video |
| GET | `/videos/{id_or_slug}/comments` | No | Commenti pubblici approvati |
| GET | `/creators` | No | Lista creators |
| GET | `/creators/{id_or_username_or_channel_slug}` | No | Dettaglio creator |
| GET | `/creators/{id_or_username_or_channel_slug}/videos` | No | Video pubblici del creator |
| GET | `/search` | No | Ricerca globale |
| POST | `/videos/{id_or_slug}/comments` | Si | Pubblica commento |
| POST | `/videos/{id_or_slug}/reaction` | Si | Like, dislike o reset reazione |
| POST | `/creators/{id_or_username_or_channel_slug}/subscribe` | Si | Toggle iscrizione canale |
| GET | `/me/watch-later` | Si | Watch later |
| POST | `/me/watch-later/{id_or_slug}` | Si | Aggiunge a watch later |
| DELETE | `/me/watch-later/{id_or_slug}` | Si | Rimuove da watch later |
| GET | `/me/history` | Si | Cronologia utente |
| POST | `/me/history/{id_or_slug}` | Si | Aggiorna progresso di visione |

## Stripe e webhook

Webhook da registrare:

- endpoint: `POST /billing/stripe/webhook`
- header richiesto: `Stripe-Signature`
- eventi supportati:
  - `checkout.session.completed`
  - `customer.subscription.created`
  - `customer.subscription.updated`
  - `customer.subscription.deleted`
  - `invoice.payment_failed`
  - `invoice.paid`

Configurazione admin:

1. Aprire `Admin > Settings`.
2. Compilare `Stripe Public Key`.
3. Compilare `Stripe Secret Key`.
4. Compilare `Stripe Webhook Secret`.
5. Impostare `Premium Price ID`, importo e valuta.

## Setup rapido

```bash
php artisan migrate
php artisan config:clear
```

## Note implementative

- Il rinnovo ricorrente resta delegato a Stripe Billing.
- Le pubblicita vengono soppresse per gli utenti premium.
- `GET /me` e `GET /me/premium` sono le fonti principali per leggere stato premium e badge.
- `profile.is_premium_subscriber` e un flag rapido per le UI che devono mostrare il badge senza reinterpretare tutto il blocco premium.
