# Globio API v1

Base URL API: `/api/v1`

Auth header: `Authorization: Bearer <token>`

## Premium mensile senza pubblicita

Globio ora supporta un piano `Globio Premium` con rinnovo mensile sicuro tramite Stripe Checkout e gestione abbonamento via Stripe Customer Portal.

Funzioni premium incluse:

- rimozione pubblicita per web e app
- background playback
- picture in picture
- quality streaming avanzata per i video
- controlli avanzati per reels
- smart downloads e queue management lato app

Flusso consigliato:

1. chiamare `GET /api/v1/premium/plans`
2. autenticare l'utente
3. chiamare `POST /api/v1/me/premium/checkout`
4. aprire `checkout_url` nell'app o webview sicura
5. dopo il redirect finale chiamare `POST /api/v1/me/premium/confirm`
6. leggere stato e capabilities da `GET /api/v1/me/premium` o `GET /api/v1/me`

Webhook Stripe:

- endpoint: `POST /billing/stripe/webhook`
- verifica firma: header `Stripe-Signature`
- aggiorna stato abbonamento, periodo corrente e accesso premium utente

Configurazione Stripe:

- apri `Admin > Settings`
- compila `Stripe Public Key`
- compila `Stripe Secret Key`
- compila `Stripe Webhook Secret`
- imposta `Premium Price ID`, importo e valuta

## Endpoint premium

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/premium/plans` | No | Restituisce piano premium e feature incluse |
| GET | `/me/premium` | Si | Stato premium corrente dell'utente |
| POST | `/me/premium/checkout` | Si | Crea sessione Stripe Checkout `mode=subscription` |
| POST | `/me/premium/confirm` | Si | Sincronizza sessione checkout completata |
| POST | `/me/premium/portal` | Si | Crea sessione Stripe Customer Portal |

Esempio checkout:

```bash
curl -X POST "https://example.com/api/v1/me/premium/checkout" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "success_url": "https://example.com/premium/success",
    "cancel_url": "https://example.com/premium/cancel"
  }'
```

Risposta:

```json
{
  "message": "Checkout session created.",
  "checkout_url": "https://checkout.stripe.com/c/pay/cs_test_...",
  "session_id": "cs_test_..."
}
```

## Playlist API

CRUD playlist app pronto per mobile e web autenticato.

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/me/playlists` | Si | Elenco playlist utente |
| POST | `/me/playlists` | Si | Crea nuova playlist |
| GET | `/me/playlists/{playlist}` | Si | Dettaglio playlist con video |
| PUT | `/me/playlists/{playlist}` | Si | Aggiorna titolo, descrizione, visibilita |
| DELETE | `/me/playlists/{playlist}` | Si | Elimina playlist |
| POST | `/me/playlists/{playlist}/videos` | Si | Aggiunge un video |
| DELETE | `/me/playlists/{playlist}/videos/{video}` | Si | Rimuove un video |

Esempio creazione playlist:

```bash
curl -X POST "https://example.com/api/v1/me/playlists" \
  -H "Authorization: Bearer TOKEN_VALUE" \
  -H "Content-Type: application/json" \
  -d '{
    "title": "I miei reels preferiti",
    "description": "Playlist personale",
    "is_public": true,
    "video_ids": [12, 18, 22]
  }'
```

## Endpoint core gia utili per app

| Metodo | Endpoint | Auth | Descrizione |
|---|---|---|---|
| GET | `/app-config` | No | Config app, feature flags e piano premium |
| GET | `/home` | No | Feed home |
| GET | `/videos` | No | Lista video o reels |
| GET | `/videos/{id_or_slug}` | No | Dettaglio video |
| GET | `/videos/{id_or_slug}/comments` | No | Commenti pubblici |
| GET | `/creators` | No | Lista creators |
| GET | `/search` | No | Ricerca globale |
| GET | `/me` | Si | Profilo utente con stato premium |
| GET | `/me/watch-later` | Si | Watch later |
| GET | `/me/history` | Si | Cronologia |
| GET | `/me/notifications` | Si | Notifiche |

## Setup rapido

```bash
php artisan migrate
php artisan config:clear
```

Se usi Stripe in test:

1. crea un prodotto ricorrente mensile in Stripe
2. copia il relativo `price_id` nel campo admin `Premium Price ID`
3. configura il webhook Stripe verso `/billing/stripe/webhook`
4. seleziona eventi `checkout.session.completed`, `customer.subscription.updated`, `customer.subscription.deleted`, `invoice.payment_failed`, `invoice.paid`

## Note implementative

- l'abbonamento premium e separato dalle iscrizioni ai canali presenti nella tabella `subscriptions`
- le pubblicita vengono soppresse per gli utenti premium
- la gestione rinnovi non usa loop manuali: il rinnovo resta delegato a Stripe Billing
- l'app puo leggere le capabilities premium direttamente da `GET /api/v1/me` e `GET /api/v1/me/premium`
