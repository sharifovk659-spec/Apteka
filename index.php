<?php

/**
 * Fallback entry when Hostinger Document Root points to the project folder
 * instead of /public. Prefer Document Root → /public in hPanel.
 */

require __DIR__.'/public/index.php';
