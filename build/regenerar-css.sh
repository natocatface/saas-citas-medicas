#!/bin/sh
# Regenera el CSS embebido tras cambios de diseño (requiere python3).
cd "$(dirname "$0")/.."
python3 build/extraer-clases.py
python3 build/gencss.py
{ echo '<style>'; echo '@verbatim'; cat public/css/app.css; echo ''; echo '@endverbatim'; echo '</style>'; } > resources/views/partials/inline-css.blade.php
echo "CSS embebido regenerado."
