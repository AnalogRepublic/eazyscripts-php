<?php


if (!function_exists('encode_request_data')) {

    /**
     * Encode strings, arrays & objects to a format
     * which can be accepted by the API.
     *
     * @param  string|array|object $what
     * @param  string|array        $old_encoding Default "UTF-8"
     * @return string|array|object
     */
    function encode_request_data($what, $old_encoding = 'UTF-8')
    {
        if (is_array($what)) {
            foreach ($what as $key => $value) {
                $what[$key] = encode_request_data($value);
            }

            return $what;
        }

        if (is_object($what)) {
            $vars = array_keys(get_object_vars($what));

            foreach ($vars as $var) {
                $what->$var = encode_request_data($what->$var);
            }

            return $what;
        }

        if (is_string($what)) {
            if (!function_exists('iconv')) {
                throw new \Exception('iconv is missing and is required to encode EazyScripts request data.');
            }

            if (!function_exists('mb_substr')) {
                throw new \Exception('mb_substr is missing and is required to encode EazyScripts request data.');
            }

            $encoded_stripped = '';
            $encoded = iconv($old_encoding, 'ASCII//IGNORE//TRANSLIT', $what);

            // Iterate the strings characters
            $j = 0;
            for ($i = 0; $i < strlen($encoded); $i++) {

                // Grab the current character.
                $current_character = $encoded[$i];

                // Grab the next character as UTF-8
                $next_character = @mb_substr($what, $j++, 1, 'UTF-8');

                // Check for illegal characters.
                if (strstr('`^~\'"', $current_character) !== false) {
                    // If we've jumped about and are comparing the same, go back
                    if ($current_character <> $next_character) {
                        --$j;
                        continue;
                    }
                }

                // Add the next character if we've got an unknown one,
                // otherwise add the current
                $encoded_stripped .= ($current_character == '?') ? $next_character : $current_character;
            }

            return $encoded_stripped;
        }

        return $what;
    }
}
