## webhook parcel status changed

```json
{
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "event_type": "parcel_status_changed",
    "detail": {
      "id": "50000000000000000000000001",
      "tracking": "MXT0000000001",
      "status": "in_destination_point",
      "status_history": [
        {
          "status": "created",
          "when": "2025-01-02T08:15:00-06:00"
        },
        {
          "status": "in_origin_point",
          "when": "2025-01-03T12:30:00-06:00"
        },
        {
          "status": "in_destination_point",
          "when": "2025-01-04T17:45:00-06:00"
        }
      ]
    }
  }
}

```


## webhook parcel origin changed

```json
{
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "event_type": "parcel_origin_changed",
    "detail": {
      "id": "50000000000000000000000001",
      "tracking": "MXT0000000001",
      "origin": {
        "id": "30000000000000000000000004",
        "external_id": "MX000003",
        "name": "John's Shop",
        "description": "Grocery",
        "address": {
          "postal_code": "32000",
          "city": "Ciudad Juárez",
          "address": "Chihuahua, Ciudad Juárez, Progresista, Calle Tío Pepe, 2",
          "coordinate": {
            "latitude": 31.7035,
            "longitude": -106.4350
          }
        },
        "schedule": "Lun, Mie, Vie: 09:30 - 17:00.",
        "enabled": true,
        "created_at": "2025-01-13T07:37:13-06:00"
      }
    }
  }
}
```

## webhook parcel origin changed

```json
{
  "headers": {
    "Content-Type": "application/json"
  },
  "body": {
    "event_type": "parcel_destination_changed",
    "detail": {
      "id": "50000000000000000000000001",
      "tracking": "MXT0000000001",
      "destination": {
        "id": "30000000000000000000000004",
        "external_id": "MX000003",
        "name": "John's Shop",
        "description": "Grocery",
        "address": {
          "postal_code": "32000",
          "city": "Ciudad Juárez",
          "address": "Chihuahua, Ciudad Juárez, Progresista, Calle Tío Pepe, 2",
          "coordinate": {
            "latitude": 31.7035,
            "longitude": -106.4350
          }
        },
        "schedule": "Lun, Mie, Vie: 09:30 - 17:00.",
        "enabled": true,
        "created_at": "2025-01-13T07:37:13-06:00"
      }
    }
  }
}
```