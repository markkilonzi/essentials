---
paths:
  - 'config/**'
---

# Config

## url for absolute addresses, uri for relative paths
Name a config key `url` (`base_url`, `api_url`) when it holds a full address with scheme and host (`https://...`). Name it `uri` (`api_uri`, `uris.template`) when it holds a relative path resolved against a base URL (`api/sms/send`). Do not mix them, for example an absolute address under `api_uri`.
