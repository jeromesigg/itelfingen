<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Fehler bei der Kommunikation mit Bexio (fehlendes/abgelaufenes Token, Fehlermeldung der API).
 */
class BexioException extends RuntimeException {}
