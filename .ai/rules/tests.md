---
paths:
  - 'tests/**'
---

# Tests

## Keine Tests ohne Rückfrage löschen
Tests und Testdateien unter tests/ nie ohne ausdrückliche Zustimmung entfernen. Sie sind keine temporären Hilfsdateien, sondern Kern der Anwendung.

## Nach grünen Tests nach der ganzen Suite fragen
Wenn die Tests zur eigenen Änderung bestehen, fragen, ob zusätzlich die ganze Suite laufen soll (php artisan test --compact).
