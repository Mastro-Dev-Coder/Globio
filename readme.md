# Flutter API v1 - Documentazione

Base URL: `/api/v1`

Autenticazione: `Authorization: Bearer <token>`

---

## Autenticazione

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| POST | `/auth/register` | Registrazione nuovo utente | `name`, `email`, `password`, `password_confirmation`, `device_name?` |
| POST | `/auth/login` | Login utente | `email`, `password`, `device_name?` |
| POST | `/auth/logout` | Logout (auth) | - |

---

## Contenuto Pubblico

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| GET | `/app-config` | Configurazione app | - |
| GET | `/home` | Home page | - |
| GET | `/videos` | Lista video | `type` (all\|video\|reel), `sort` (latest\|trending\|popular), `q?`, `creator?`, `per_page=20` |
| GET | `/videos/{id_or_slug}` | Dettagli video | `id_or_slug` (id numerico o slug) |
| GET | `/videos/{id_or_slug}/comments` | Commenti video | `id_or_slug` |
| GET | `/creators` | Lista creatori | `q?`, `per_page=20` |
| GET | `/creators/{id_or_username_or_channel_slug}` | Profilo creator | `id_or_username_or_channel_slug` |
| GET | `/creators/{id_or_username_or_channel_slug}/videos` | Video del creator | `id_or_username_or_channel_slug`, `type` (all\|video\|reel) |
| GET | `/search` | Ricerca globale | `q`, `limit=10` |

---

## Azioni Contenuto Autenticate

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| POST | `/videos/{id_or_slug}/comments` | Aggiungi commento | `content`, `parent_id?` |
| POST | `/videos/{id_or_slug}/reaction` | Reazione al video | `reaction` (like\|dislike\|none) |
| POST | `/creators/{id_or_username_or_channel_slug}/subscribe` | Iscrivi/Disiscrivi | toggle subscribe/unsubscribe |

---

## Account

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| GET | `/me` | Profilo utente | - |
| PUT | `/me` | Aggiorna profilo | `name?`, `channel_name?`, `channel_description?`, `country?` |

---

## Libreria

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| GET | `/me/watch-later` | Video da guardare | - |
| POST | `/me/watch-later/{id_or_slug}` | Aggiungi a watch later | `id_or_slug` |
| DELETE | `/me/watch-later/{id_or_slug}` | Rimuovi da watch later | `id_or_slug` |
| GET | `/me/history` | Cronologia video | - |
| POST | `/me/history/{id_or_slug}` | Aggiungi a cronologia | `watched_duration`, `total_duration?`, `completed?` |
| GET | `/me/playlists` | Playlist utente | - |

---

## Notifiche

| Metodo | Endpoint | Descrizione | Parametri |
|--------|----------|--------------|-------------|-----------|
| GET | `/me/notifications` | Lista notifiche | - |
| POST | `/me/notifications/read-all` | Leggi tutte | - |
| POST | `/me/notifications/{id}/read` | Leggi notifica | `id` |

---

## Note

- **Lookup Video**: supporta sia `id` numerico che slug (`video_url`)
- **Lookup Creator**: supporta `id` numerico, `username` o slug del canale
- **Media URL**: restituiti come URL assoluti, pronti per il client
- Parametri con `?` sono opzionali