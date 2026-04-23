# Flutter API v1

Base URL: `/api/v1`

Auth: `Authorization: Bearer <token>`

## Auth

- `POST /auth/register`
  - body: `name`, `email`, `password`, `password_confirmation`, `device_name?`
- `POST /auth/login`
  - body: `email`, `password`, `device_name?`
- `POST /auth/logout` (auth)

## Public Content

- `GET /app-config`
- `GET /home`
- `GET /videos?type=all|video|reel&sort=latest|trending|popular&q=&creator=&per_page=20`
- `GET /videos/{id_or_slug}`
- `GET /videos/{id_or_slug}/comments`
- `GET /creators?q=&per_page=20`
- `GET /creators/{id_or_username_or_channel_slug}`
- `GET /creators/{id_or_username_or_channel_slug}/videos?type=all|video|reel`
- `GET /search?q=...&limit=10`

## Authenticated Content Actions

- `POST /videos/{id_or_slug}/comments`
  - body: `content`, `parent_id?`
- `POST /videos/{id_or_slug}/reaction`
  - body: `reaction` (`like`, `dislike`, `none`)
- `POST /creators/{id_or_username_or_channel_slug}/subscribe`
  - toggle subscribe/unsubscribe

## Account

- `GET /me`
- `PUT /me`
  - body: `name?`, `channel_name?`, `channel_description?`, `country?`

## Library

- `GET /me/watch-later`
- `POST /me/watch-later/{id_or_slug}`
- `DELETE /me/watch-later/{id_or_slug}`
- `GET /me/history`
- `POST /me/history/{id_or_slug}`
  - body: `watched_duration`, `total_duration?`, `completed?`
- `GET /me/playlists`

## Notifications

- `GET /me/notifications`
- `POST /me/notifications/read-all`
- `POST /me/notifications/{id}/read`

Returns notifications from both the Laravel `notifications` table and the legacy `app_notifications` table. Each item includes `id`, `source`, `title`, `message`, `type`, `action_url`, `data`, `read_at`, `created_at`.

## Reports

- `GET /reports/reasons?target_type=user|video|comment|channel` (auth)
- `POST /reports` (auth)
  - body: `target_type`, `target_id`, `reason`, `type?`, `description?`

## Studio

- `GET /me/studio/summary`
- `GET /me/studio/analytics?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD&limit=10`
- `GET /me/studio/analytics/videos/{id_or_slug}?start_date=YYYY-MM-DD&end_date=YYYY-MM-DD`
- `GET /me/studio/community?status=all|approved|pending|rejected|hidden&per_page=20`
- `POST /me/studio/community/comments/{id}/approve`
- `POST /me/studio/community/comments/{id}/reject`
- `POST /me/studio/community/comments/{id}/hide`
- `GET /me/studio/reports?view=received|submitted&status=all|pending|reviewed|resolved|dismissed|escalated`
- `GET /me/studio/feedback?status=all|read|unread`
- `POST /me/studio/feedback/{id}/read`

## Notes

- Video lookup supports both numeric `id` and `video_url` slug.
- Creator lookup supports numeric `id`, `username`, and channel-name slug.
- Media URLs are returned as absolute URLs and already client-ready.
