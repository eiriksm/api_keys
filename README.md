# API keys authentication provider for Drupal 8 REST module

## Example config

```
resources:
  'entity:log':
    GET:
      supported_formats:
        - hal_json
      supported_auth:
        - api_keys
    POST:
      supported_formats:
        - hal_json
      supported_auth:
        - api_keys
    PATCH:
      supported_formats:
        - hal_json
      supported_auth:
        - api_keys
    DELETE:
      supported_formats:
        - hal_json
      supported_auth:
        - api_keys
```

## Example POST request:

```
curl -H "Content-Type: application/hal+json" -H "x-drupal-api-key: 1337TR0ll0ll0lOl" http://example.com/entity/log -d '{"_links": {"type": {"href": "http://example.com/rest/type/log/temperature"}},"name": [{"value": "test"}]}'
```

This assumes you have configured the REST resource for entity type "log" and a bundle called "temperature". If you have just added the bundle "temperature", you must clear the cache.
